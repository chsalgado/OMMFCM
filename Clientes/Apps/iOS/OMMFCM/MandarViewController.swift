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
    
    var envioExitoso: Bool?
    
    @IBAction func botonEnviarDatos(sender: UIButton)
    {
        self.enviarDatos()
    }    
    
    override func viewDidLoad()
    {
        super.viewDidLoad()
        self.imagen.image = Datos.imagen
        self.colocaPuntoEnMapa()
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
    
    func enviarDatos()
    {
        // Datos a enviar
        // Foto
        Datos.imagen = self.corregirOrientacion(Datos.imagen!)
        let datosFoto = UIImageJPEGRepresentation(Datos.imagen!, 0.5)
        let imgb64 = datosFoto?.base64EncodedStringWithOptions(.Encoding64CharacterLineLength)
        
        var incidente: [String: String] = ["extension": ".jpeg", "imagen": imgb64!, "fecha": Datos.fecha!]
        
        // Coordenadas
        if Datos.latitud != nil && Datos.longitud != nil {
            incidente["lat"] = Datos.latitud!.description
            incidente["long"] = Datos.longitud!.description
        }
        
        let datosJson = try? NSJSONSerialization.dataWithJSONObject(incidente, options: .PrettyPrinted)
        
        // URL del servicio y objeto sesion
        let urlServicio = "http://watch.imt.mx/public_html/index.php/api/incidentes"
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
                if response != nil && (response as! NSHTTPURLResponse).statusCode == 200 {
                    self.performSegueWithIdentifier("vistaFinal", sender: self)
                    self.envioExitoso = true
                } else  {
                    let respuesta = UIAlertController(title: "Error", message: error?.localizedDescription, preferredStyle: UIAlertControllerStyle.Alert)
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
    
    // Se redibuja la imagen si es necesario para que quede en la orientacion correcta
    // Esto es porque iOS y OSX no rotan la imagen y solo guardan en metadata su orientacion (EXIF)
    func corregirOrientacion(imagen:UIImage) -> UIImage
    {
        // Si la foto esta en orientacion correcta se regresa la misma
        if (imagen.imageOrientation == UIImageOrientation.Up)
        {
            return imagen;
        }
        
        // Se redibuja la imagen tomando en cuenta solo los graficos
        UIGraphicsBeginImageContextWithOptions(imagen.size, false, imagen.scale);
        let rect = CGRect(x: 0, y: 0, width: imagen.size.width, height: imagen.size.height)
        imagen.drawInRect(rect)
        let imagenNormal : UIImage = UIGraphicsGetImageFromCurrentImageContext()
        UIGraphicsEndImageContext();
        return imagenNormal;
    }
}