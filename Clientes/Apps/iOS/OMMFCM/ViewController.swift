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
    static var municipioOrigen: String?
    static var municipioDestino: String?
    static var kilometros: String?
    
    static var municipioOrigenTexto: String?
    static var municipioDestinoTexto: String?
    
    static func borrar()
    {
        self.imagen = nil
        self.latitud = nil
        self.longitud = nil
        self.municipioOrigen = nil
        self.municipioDestino = nil
        self.kilometros = nil
        self.municipioOrigenTexto = nil
        self.municipioDestinoTexto = nil
    }
}

import UIKit
import AssetsLibrary
import CoreLocation

class ViewController: UIViewController, UIImagePickerControllerDelegate, UINavigationControllerDelegate
{
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
        self.solicitarEstados()
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
                        self.siguienteVista()
                    }
                }, failureBlock:
                {
                    (error: NSError!) in
                    NSLog("Error!")
                })
            
        }
        else
        {
            self.siguienteVista()
        }
    }