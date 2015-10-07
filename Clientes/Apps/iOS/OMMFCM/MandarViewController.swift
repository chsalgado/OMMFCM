//
//  MandarViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 17/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

import UIKit
import MapKit

class MandarViewController: UIViewController
{
    @IBOutlet weak var imagen: UIImageView!
    
    @IBOutlet weak var mapa: MKMapView!
    @IBOutlet weak var tituloSinCoordenadas: UILabel!
    
    @IBOutlet weak var vistaInformacion: UIView!
    @IBOutlet weak var municipioOrigen: UILabel!
    @IBOutlet weak var municipioDestino: UILabel!
    @IBOutlet weak var kilometros: UILabel!
    
    var envioExitoso: Bool?
    
    @IBAction func botonEnviarDatos(sender: UIButton)
    {
        self.enviarDatos()
    }    
    
    override func viewDidLoad()
    {
        super.viewDidLoad()
        self.imagen.image = Datos.imagen
        
        if Datos.latitud != nil
        {
            self.colocaPuntoEnMapa()
            self.tituloSinCoordenadas.hidden = true
        }
        else
        {
            self.mapa.hidden = true
        }
        
        if Datos.municipioDestino != nil
        {
            self.colocaInformacion()
        }
        else
        {
            self.vistaInformacion.hidden = true
        }
    }
    
    func colocaPuntoEnMapa()
    {
        let punto = MKPointAnnotation()
        punto.title = "Incidente"
        punto.coordinate.latitude = Datos.latitud!
        punto.coordinate.longitude = Datos.longitud!
        self.mapa.addAnnotation(punto)
        
        let regionRadius: CLLocationDistance = 1000
        let coordinateRegion = MKCoordinateRegionMakeWithDistance(punto.coordinate,regionRadius * 5.0, regionRadius * 5.0)
        self.mapa.setRegion(coordinateRegion, animated: true)
    }
    
    func colocaInformacion()
    {
        self.municipioOrigen.text = Datos.municipioOrigenTexto
        self.municipioDestino.text = Datos.municipioDestinoTexto
        self.kilometros.text = Datos.kilometros
    }
    
    func enviarDatos()
    {
        // Datos a enviar
        // Foto
        let datosFoto = UIImageJPEGRepresentation(Datos.imagen!, 0.5)
        let imgb64 = datosFoto?.base64EncodedStringWithOptions(.Encoding64CharacterLineLength)
        
        var incidente: [String: String] = ["extension": ".jpeg", "imagen": imgb64!]
        
        // Coordenadas
        if Datos.latitud != nil && Datos.longitud != nil {
            incidente["lat"] = Datos.latitud!.description
            incidente["long"] = Datos.longitud!.description
        }
        // Municipios
        if Datos.municipioOrigen != nil && Datos.municipioDestino != nil && Datos.kilometros != nil {
            incidente["mpioOrigen"] = Datos.municipioOrigen
            incidente["mpioDestino"] = Datos.municipioDestino
            incidente["km"] = Datos.kilometros
        }
        
        let datosJson = try? NSJSONSerialization.dataWithJSONObject(incidente, options: .PrettyPrinted)
        
        // URL del servicio y objeto sesion
        let urlServicio = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/incidentes"
        let url = NSURL(string: urlServicio)
        let sesion = NSURLSession(configuration: NSURLSessionConfiguration.defaultSessionConfiguration())
        
        // Solicitud
        let request = NSMutableURLRequest(URL: url!)
        request.HTTPMethod = "POST"
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.addValue("application/json", forHTTPHeaderField: "Accept")
        request.HTTPBody = datosJson
        
        if datosJson != nil
        {
            let aviso = UIAlertController(title: "Enviando...", message: "", preferredStyle: UIAlertControllerStyle.Alert)
            let accion = UIAlertAction(title: "Cerrar", style: UIAlertActionStyle.Default, handler: { _ in })
            aviso.addAction(accion)
            self.presentViewController(aviso, animated: true, completion: {})
            
            sesion.dataTaskWithRequest(request, completionHandler: { (data, response, error) -> Void in
                self.presentedViewController?.dismissViewControllerAnimated(true, completion: nil)
                let accion = UIAlertAction(title: "Aceptar", style: UIAlertActionStyle.Default, handler: { _ in })
                if (response as! NSHTTPURLResponse).statusCode == 200 {
                    let respuesta = UIAlertController(title: "Enviado", message: "", preferredStyle: UIAlertControllerStyle.Alert)
                    respuesta.addAction(accion)
                    self.presentViewController(respuesta, animated: true, completion: {})
                    self.envioExitoso = true
                } else  {
                    let respuesta = UIAlertController(title: "Error", message: error?.description, preferredStyle: UIAlertControllerStyle.Alert)
                    respuesta.addAction(accion)
                    self.presentViewController(respuesta, animated: true, completion: {})
                    self.envioExitoso = false
                }
            }).resume()
        }
        else
        {
            print("error: no pudo convertir a Json")
            self.envioExitoso = false
        }
    }
}