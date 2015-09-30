//
//  MapaViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 22/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

import UIKit
import MapKit

class MapaViewController: UIViewController
{
    @IBOutlet weak var mapa: MKMapView!
    
    var ubicacion: MKPointAnnotation?
    
    @IBAction func tocoUbicacion(sender: UITapGestureRecognizer)
    {
        let puntoToque = sender.locationInView(self.mapa)
        let coordenadas: CLLocationCoordinate2D = mapa.convertPoint(puntoToque, toCoordinateFromView: self.mapa)
        
        if self.ubicacion == nil
        {
            self.inicializaAnotacion()
        }
        self.ubicacion!.coordinate = coordenadas
        self.actualizaCoordenadas()
    }
    
    func actualizaCoordenadas()
    {
        Datos.latitud = self.ubicacion?.coordinate.latitude
        Datos.longitud = self.ubicacion?.coordinate.longitude
        print(Datos.longitud)
    }
    
    func inicializaAnotacion()
    {
        self.ubicacion = MKPointAnnotation()
        self.ubicacion!.title = "Incidente"
        self.mapa.addAnnotation(self.ubicacion!)
    }
    
    override func viewDidLoad()
    {
        super.viewDidLoad()
        if Datos.longitud != nil
        {
            self.inicializaAnotacion()
            self.ubicacion!.coordinate.latitude = Datos.latitud!
            self.ubicacion!.coordinate.longitude = Datos.longitud!
        }
    }
    
    override func prefersStatusBarHidden() -> Bool
    {
        return true
    }
}
