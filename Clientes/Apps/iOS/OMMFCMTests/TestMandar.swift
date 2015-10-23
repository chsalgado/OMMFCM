//
//  TestMandar.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 06/10/15.
//  Copyright © 2015 imt. All rights reserved.
//

import XCTest
@testable import OMMFCM

class TestMandar: XCTestCase {
    var controladorMandar: MandarViewController!
    
    override func setUp() {
        super.setUp()
        // Put setup code here. This method is called before the invocation of each test method in the class.
        Datos.latitud = 0.0
        Datos.longitud = 0.0
        let sb = UIStoryboard(name: "Main", bundle: nil)
        self.controladorMandar = sb.instantiateViewControllerWithIdentifier("MandarViewController") as! MandarViewController
        let _ = controladorMandar.view
    }
    
    override func tearDown() {
        // Put teardown code here. This method is called after the invocation of each test method in the class.
        super.tearDown()
    }
    
    func testColocaPuntoEnMapa() {
        let latitudPrueba = 35.122000
        let longitudPrueba = -100.122000
        Datos.latitud = latitudPrueba
        Datos.longitud = longitudPrueba
        
        self.controladorMandar.colocaPuntoEnMapa()
        
        let anotation = self.controladorMandar.mapa.annotations.first
        XCTAssertEqual(anotation?.coordinate.latitude, latitudPrueba)
        XCTAssertEqual(anotation?.coordinate.longitude, longitudPrueba)
    }
    
    func testEnviarDatos() {
        let latitudPrueba = 35.122000
        let longitudPrueba = -100.122000
        Datos.latitud = latitudPrueba
        Datos.longitud = longitudPrueba
        Datos.imagen = UIImage(named: "prueba.jpg", inBundle: NSBundle(forClass: TestMandar.self), compatibleWithTraitCollection: nil)
        
        XCTAssertNil(self.controladorMandar.envioExitoso)
        
        self.controladorMandar.enviarDatos()
        
        let expectativa = expectationWithDescription("ruta creada")
        self.performBlock({
            if self.controladorMandar.envioExitoso!
            {
                expectativa.fulfill()
            }}, afterDelay: 3)
        
        self.waitForExpectationsWithTimeout(3.5) { (error) -> Void in
            XCTAssertNotNil(self.controladorMandar.envioExitoso)
        }
    }
    
    // Ejecuta un bloque de codigo despues del tiempo dado
    func performBlock(block:() -> Void, afterDelay delay:NSTimeInterval){
        dispatch_after(dispatch_time(DISPATCH_TIME_NOW, Int64(delay * Double(NSEC_PER_SEC))), dispatch_get_main_queue(), block)
    }
}
