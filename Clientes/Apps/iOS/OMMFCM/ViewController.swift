//
//  ViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 17/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

struct Datos {
    static var imagen: UIImage?
    static var latitud: CLLocationDegrees?
    static var longitud: CLLocationDegrees?
    
    static func borrar() {
        self.imagen = nil
        self.latitud = nil
        self.longitud = nil
    }
}

import UIKit
import AssetsLibrary
import CoreLocation

class ViewController: UIViewController, UIImagePickerControllerDelegate, UINavigationControllerDelegate {
    
    @IBAction func botonTomarFoto(sender: UIButton) {
        self.mostrarCamara()
    }
    
    @IBAction func botonSeleccionaFoto(sender: UIButton) {
        self.mostrarGaleria()
    }
    
    // Unwind segue
    @IBAction func regresaAPrincipal(segue: UIStoryboardSegue) {
        
    }
    
    func revisarDatos() {
        self.performSegueWithIdentifier("verificarDatos", sender: self)
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func mostrarCamara() {
        let picker = UIImagePickerController()
        picker.sourceType = UIImagePickerControllerSourceType.Camera
        picker.delegate = self
        self.presentViewController(picker, animated: true, completion: nil)
    }
    
    func mostrarGaleria() {
        let picker = UIImagePickerController()
        picker.sourceType = UIImagePickerControllerSourceType.PhotoLibrary
        picker.delegate = self
        self.presentViewController(picker, animated: true, completion: nil)
    }
    
    func imagePickerController(picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : AnyObject]) {
        Datos.borrar()
        //Se quita la vista: camara o galeria
        picker.dismissViewControllerAnimated(true, completion: nil)
        Datos.imagen = info[UIImagePickerControllerOriginalImage] as? UIImage
        
        //obtengo coordenadas
        let library = ALAssetsLibrary()
        let url: NSURL? = info[UIImagePickerControllerReferenceURL] as? NSURL
        if url != nil {
            library.assetForURL(url, resultBlock:
                {
                    (asset: ALAsset!) in
                    if asset.valueForProperty(ALAssetPropertyLocation) != nil {
                        Datos.latitud = (asset.valueForProperty(ALAssetPropertyLocation) as! CLLocation!).coordinate.latitude
                        Datos.longitud = (asset.valueForProperty(ALAssetPropertyLocation) as! CLLocation!).coordinate.longitude
                        print(Datos.latitud)
                        print(Datos.longitud)
                    }
                }, failureBlock:
                {
                    (error: NSError!) in
                    NSLog("Error!")
                })
            
        }
        //me voy a la siguiente vista
        self.revisarDatos()
    }
    
    func imagePickerControllerDidCancel(picker: UIImagePickerController) {
        picker.dismissViewControllerAnimated(true, completion: {})
    }
}

