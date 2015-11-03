//
//  ViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 17/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

struct Datos
{
    static var imagen: UIImage?
    static var latitud: CLLocationDegrees?
    static var longitud: CLLocationDegrees?
    static var fecha: String?
    
    static func borrar()
    {
        self.imagen = nil
        self.latitud = nil
        self.longitud = nil
        self.fecha = nil
    }
}

import UIKit
import AssetsLibrary
import CoreLocation

class ViewController: UIViewController, UIImagePickerControllerDelegate, UINavigationControllerDelegate
{
    // Unwind segue, para regresar aqui
    @IBAction func regresaAInicio(segue: UIStoryboardSegue) {}
    
    @IBAction func botonTomarFoto(sender: UIButton)
    {
        self.mostrarCamara()
    }
    
    @IBAction func botonSeleccionaFoto(sender: UIButton)
    {
        self.mostrarGaleria()
    }
    
    func siguienteVista()
    {
        if Datos.fecha == nil {
            Datos.fecha = self.formatoAFecha(NSDate())
        }
        
        if Datos.latitud != nil
        {
            self.performSegueWithIdentifier("confirmarFoto", sender: self)
        }
        else
        {
            self.performSegueWithIdentifier("seleccionaUbicacion", sender: self)
        }
    }
    
    override func viewDidLoad()
    {
        super.viewDidLoad()
    }

    override func didReceiveMemoryWarning()
    {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func mostrarCamara()
    {
        let picker = UIImagePickerController()
        picker.sourceType = UIImagePickerControllerSourceType.Camera
        picker.delegate = self
        self.presentViewController(picker, animated: true, completion: nil)
    }
    
    func mostrarGaleria()
    {
        let picker = UIImagePickerController()
        picker.sourceType = UIImagePickerControllerSourceType.PhotoLibrary
        picker.delegate = self
        self.presentViewController(picker, animated: true, completion: nil)
    }
    
    func imagePickerController(picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : AnyObject])
    {
        Datos.borrar()
        // Se quita la vista: camara o galeria
        picker.dismissViewControllerAnimated(true, completion: nil)
        Datos.imagen = info[UIImagePickerControllerOriginalImage] as? UIImage
        
        // Guarda la foto si se tomo con la camara
        if picker.sourceType == UIImagePickerControllerSourceType.Camera {
            UIImageWriteToSavedPhotosAlbum(Datos.imagen!, nil, nil, nil)
        }
        
        // obtengo coordenadas
        let library = ALAssetsLibrary()
        let url: NSURL? = info[UIImagePickerControllerReferenceURL] as? NSURL
        if url != nil {
            library.assetForURL(url, resultBlock:
                {
                    (asset: ALAsset!) in
                    if asset.valueForProperty(ALAssetPropertyLocation) != nil
                    {
                        Datos.latitud = (asset.valueForProperty(ALAssetPropertyLocation) as! CLLocation!).coordinate.latitude
                        Datos.longitud = (asset.valueForProperty(ALAssetPropertyLocation) as! CLLocation!).coordinate.longitude
                    }
                    if asset.valueForProperty(ALAssetPropertyDate) != nil
                    {
                        Datos.fecha = self.formatoAFecha(asset.valueForProperty(ALAssetPropertyDate) as! NSDate)
                    }
                    self.siguienteVista()
                }, failureBlock:
                {
                    (error: NSError!) in
                    print(error)
                })
        }
        else
        {
            self.siguienteVista()
        }
    }
    
    func imagePickerControllerDidCancel(picker: UIImagePickerController)
    {
        picker.dismissViewControllerAnimated(true, completion: {})
    }
    
    func formatoAFecha(fecha: NSDate) -> String {
        let formato = NSDateFormatter()
        formato.dateFormat = "yyyy-MM-dd HH:mm:ss"
        formato.timeZone = NSTimeZone()
        return formato.stringFromDate(fecha)
    }
}