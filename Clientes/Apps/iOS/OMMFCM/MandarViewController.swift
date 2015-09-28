//
//  MandarViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 17/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

import UIKit

class MandarViewController: UIViewController {
    @IBOutlet weak var imagen: UIImageView!
    
    @IBAction func botonMandarFoto(sender: UIButton) {
        self.enviarDatos()
    }    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.imagen.image = Datos.imagen
    }
    
    func enviarDatos() {
        //URL del servicio y objeto sesion
        let url = NSURL(string: "webserviceurl")    //MANU URL SERVICIO WEB
        let sesion = NSURLSession(configuration: NSURLSessionConfiguration.defaultSessionConfiguration())
        
        //solicitud
        let request = NSMutableURLRequest(URL: url!)
        request.HTTPMethod = "POST"
        
        //datos a enviar
        if Datos.longitud != nil {
            let lat = Double(Datos.latitud!)
            let long = Double(Datos.longitud!)
            let datosFoto = UIImagePNGRepresentation(Datos.imagen!)
            let imgb64 = datosFoto?.base64EncodedStringWithOptions(.Encoding64CharacterLineLength)
            
            let datos: [String: AnyObject] = ["fecha" : "", "idEspecie": "", "long": long, "lat": lat, "mpioOrigen": "", "mpioDestino": "", "km": "", "imagen": imgb64!]
            
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
            } else {
                print("error: no pudo convertir a Json")
            }
        } else {
            print("no tiene coordenadas")
            
        }
    }
    
}