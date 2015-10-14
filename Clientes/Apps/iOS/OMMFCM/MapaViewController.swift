//
//  MapaViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 22/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

import UIKit
import MapKit
import CoreLocation

class MapaViewController: UIViewController, MKMapViewDelegate
{
    @IBOutlet weak var mapa: MKMapView!
    
    var ubicacion: MKPointAnnotation?
    var municipioOrigen: CLPlacemark? = nil
    {
        didSet
        {
            if self.municipioOrigen != nil && self.municipioDestino != nil
            {
                self.trazaRuta()
            }
        }
    }
    var municipioDestino: CLPlacemark? = nil
    {
        didSet
        {
            if self.municipioDestino != nil && self.municipioOrigen != nil
            {
                self.trazaRuta()
            }
        }
    }
    var ruta: MKRoute?
    
    var nombreOrigen: String?
    var nombreDestino: String?
    
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
        self.mapa.delegate = self
        if Datos.longitud != nil
        {
            self.inicializaAnotacion()
            self.ubicacion!.coordinate.latitude = Datos.latitud!
            self.ubicacion!.coordinate.longitude = Datos.longitud!
        }
        self.localizarPuntos()
    }
    
    override func prefersStatusBarHidden() -> Bool
    {
        return true
    }
    
    func localizarPuntos()
    {
        if self.nombreOrigen != nil
        {
            let geocoderOrigen = CLGeocoder()
            let textoOrigen = self.nombreOrigen! + " méxico"
            geocoderOrigen.geocodeAddressString(textoOrigen, completionHandler: {(placemark: [CLPlacemark]?, error: NSError?) in
                if (error != nil)
                {
                    print(error)
                }
                else
                {
                    self.municipioOrigen = placemark?.last
                }
            })
        }
        
        if self.nombreDestino != nil
        {
            let geocoderDestino = CLGeocoder()
            let textoDestino = self.nombreDestino! + " méxico"
            geocoderDestino.geocodeAddressString(textoDestino, completionHandler: {(placemark: [CLPlacemark]?, error: NSError?) in if (error != nil)
            {
                print(error)
            }
            else
            {
                self.municipioDestino = placemark?.last
                }
            })
        }
    }
    
    func trazaRuta()
    {
        let inicio = MKMapItem(placemark: MKPlacemark(placemark: self.municipioOrigen!))
        let fin = MKMapItem(placemark: MKPlacemark(placemark: self.municipioDestino!))
        let request = MKDirectionsRequest()
        request.source = inicio
        request.destination = fin
        request.transportType = .Automobile
        
        let directions = MKDirections(request: request)
        directions.calculateDirectionsWithCompletionHandler({(response: MKDirectionsResponse?, error: NSError?) in
            if (error != nil)
            {
                print(error)
            }
            else
            {
                if let rutaTrazada: MKRoute = response?.routes[0]
                {
                    self.ruta = rutaTrazada
                    self.mapa?.addOverlay(rutaTrazada.polyline)
                    let anIn = MKPointAnnotation()
                    anIn.coordinate = (self.municipioOrigen?.location?.coordinate)!
                    let anFin = MKPointAnnotation()
                    anFin.coordinate = (self.municipioDestino?.location?.coordinate)!
                    self.mapa?.showAnnotations([anIn, anFin], animated: true)
                }
            }
        })
    }
    
    func mapView(mapView: MKMapView, rendererForOverlay overlay: MKOverlay) -> MKOverlayRenderer
    {
        let myLineRenderer = MKPolylineRenderer(polyline: (self.ruta?.polyline)!)
        myLineRenderer.strokeColor = UIColor.greenColor()
        myLineRenderer.lineWidth = 3
        return myLineRenderer
    }
    
    @IBAction func botonAceptar(sender: UIButton)
    {
        if Datos.latitud != nil && Datos.longitud != nil
        {
            self.performSegueWithIdentifier("mapaConfirmar", sender: self)
        }
        else
        {
            let aviso = UIAlertController(title: "Error: Falta ubicación", message: "Toca en el mapa para seleccionar ubicación", preferredStyle: UIAlertControllerStyle.Alert)
            let accion = UIAlertAction(title: "Aceptar", style: UIAlertActionStyle.Default, handler: { _ in })
            aviso.addAction(accion)
            self.presentViewController(aviso, animated: true, completion: {})
        }
    }
    
}
