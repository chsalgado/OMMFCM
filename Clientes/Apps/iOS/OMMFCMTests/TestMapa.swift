//
//  TestMapa.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 05/10/15.
//  Copyright © 2015 imt. All rights reserved.
//

import XCTest
import MapKit
@testable import OMMFCM

class TestMapa: XCTestCase {
    let controladorMapa = MapaViewController()
    var plOrigen: CLPlacemark?
    var plDestino: CLPlacemark?
    
    override func setUp() {
        super.setUp()
        // Put setup code here. This method is called before the invocation of each test method in the class.
        controladorMapa.mapa = MKMapView(frame: CGRect(x: 0, y: 0, width: 100, height: 100))
        controladorMapa.mapa.delegate = controladorMapa
    }
    
    override func tearDown() {
        // Put teardown code here. This method is called after the invocation of each test method in the class.
        super.tearDown()
    }
    
    // Prueba que la funcion sea capaz de localizar puntos en el mapa, necesita internet
    func testLocalizaPuntos() {
        let expectativa = expectationWithDescription("ambas variables con valor")
        
        self.controladorMapa.nombreOrigen = "celaya guanajuato"
        self.controladorMapa.nombreDestino = "queretaro queretaro"
        
        self.controladorMapa.localizarPuntos()
        
        performBlock({ () -> Void in
            if self.controladorMapa.municipioOrigen != nil && self.controladorMapa.municipioDestino != nil {
                expectativa.fulfill()
            }
            }, afterDelay: 0.9)
        
        self.waitForExpectationsWithTimeout(1, handler: { error in
            XCTAssertNil(error, "Timeout")
            XCTAssertNotNil(self.controladorMapa.municipioOrigen)
            XCTAssertNotNil(self.controladorMapa.municipioDestino)
            XCTAssertEqual(self.controladorMapa.municipioOrigen?.location?.coordinate.latitude, 20.5188718)
            XCTAssertEqual(self.controladorMapa.municipioDestino?.location?.coordinate.latitude, 20.5875776)
        })
    }
    
    // Prueba que la funcion pueda trazar una ruta a partir de dos puntos dados, necesita internet
    func testTrazaRuta() {
        self.creaPlacemarks()
        let expectativa = expectationWithDescription("ruta creada")
        
        performBlock({ () -> Void in
            self.controladorMapa.municipioOrigen = self.plOrigen
            self.controladorMapa.municipioDestino = self.plDestino
            self.controladorMapa.trazaRuta()
            }, afterDelay: 1)
        
        performBlock({ () -> Void in
            if self.controladorMapa.ruta != nil {
                expectativa.fulfill()
            }
            }, afterDelay: 2.4)
        
        self.waitForExpectationsWithTimeout(2.5, handler: { error in
            XCTAssertNil(error, "Timeout")
            XCTAssertNotNil(self.controladorMapa.ruta)
        })
    }
    
    // Prueba que la funcion actualice las coordenadas
    func testActualizaCoordenadas() {
        let latitudPrueba = 35.122000
        let longitudPrueba = -100.122000
        
        self.controladorMapa.ubicacion = MKPointAnnotation()
        self.controladorMapa.ubicacion?.coordinate.latitude = latitudPrueba
        self.controladorMapa.ubicacion?.coordinate.longitude = longitudPrueba
        
        self.controladorMapa.actualizaCoordenadas()
        
        XCTAssertEqual(Datos.latitud, latitudPrueba)
        XCTAssertEqual(Datos.longitud, longitudPrueba)
    }
    
    // Prueba que la funcion inicialice la anotacion
    func testInicializaAnotacion() {
        XCTAssertNil(self.controladorMapa.ubicacion)
        self.controladorMapa.inicializaAnotacion()
        XCTAssertNotNil(self.controladorMapa.ubicacion)
    }
    
    // Funciones de apoyo, crea los puntos en el mapa que se necesitan para probar otras funciones
    func creaPlacemarks() {
        let geocoderOrigen = CLGeocoder()
        geocoderOrigen.geocodeAddressString("celaya guanajuato mexico", completionHandler: {(placemark: [CLPlacemark]?, error: NSError?) in
            XCTAssertNil(error)
            self.plOrigen = placemark?.last
        })
        
        let geocoderDestino = CLGeocoder()
        geocoderDestino.geocodeAddressString("queretaro queretaro mexico", completionHandler: {(placemark: [CLPlacemark]?, error: NSError?) in
            XCTAssertNil(error)
            self.plDestino = placemark?.last
        })
    }
    
    // Ejecuta un bloque de codigo despues del tiempo dado
    func performBlock(block:() -> Void, afterDelay delay:NSTimeInterval){
        dispatch_after(dispatch_time(DISPATCH_TIME_NOW, Int64(delay * Double(NSEC_PER_SEC))), dispatch_get_main_queue(), block)
    }
}
