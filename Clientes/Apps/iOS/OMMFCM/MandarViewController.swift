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
        // URL del servicio y objeto sesion
        let url = NSURL(string: "webserviceurl")    // TODO
        let sesion = NSURLSession(configuration: NSURLSessionConfiguration.defaultSessionConfiguration())
        
        // solicitud
        let request = NSMutableURLRequest(URL: url!)
        request.HTTPMethod = "POST"
        
        // datos a enviar
        // coordenadas
        let lat = Datos.latitud != nil ? Double(Datos.latitud!).description : ""
        let long = Datos.longitud != nil ?  Double(Datos.longitud!).description : ""
        
        // informacion
        let municipioOri = Datos.municipioOrigen != nil ? Datos.municipioOrigen : ""
        let municipioDes = Datos.municipioDestino != nil ? Datos.municipioDestino : ""
        let kilomts = Datos.kilometros != nil ? Datos.kilometros : ""
        
        // foto
        let datosFoto = UIImagePNGRepresentation(Datos.imagen!)
        let imgb64 = datosFoto?.base64EncodedStringWithOptions(.Encoding64CharacterLineLength)
        
        let datos: [String: String] = ["fecha" : "", "idEspecie": "", "long": long, "lat": lat, "mpioOrigen": municipioOri!, "mpioDestino": municipioDes!, "km": kilomts!, "imagen": imgb64!, "extension": ".png"]
        
        /*  Prueba que es un JSON valido
        let valid = NSJSONSerialization.isValidJSONObject(datos)
        print(valid)
        */
        
        let datosJson = try? NSJSONSerialization.dataWithJSONObject(datos, options: .PrettyPrinted)
        if datosJson != nil {
            sesion.uploadTaskWithRequest(request, fromData: datosJson, completionHandler: {(data: NSData?,
                response: NSURLResponse?,
                error: NSError?) in
                print(data)
                print(response)
                print(error)
            }).resume()
            print("envio los datos")
        }
        else
        {
            print("error: no pudo convertir a Json")
        }
        
        
    }
}