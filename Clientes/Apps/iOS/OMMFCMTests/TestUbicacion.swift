//
//  TestUbicacion.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 06/10/15.
//  Copyright © 2015 imt. All rights reserved.
//

import XCTest
@testable import OMMFCM

class TestUbicacion: XCTestCase {
    var controladorUbicacion: UbicacionViewController!
    
    override func setUp() {
        super.setUp()
        let sb = UIStoryboard(name: "Main", bundle: nil)
        self.controladorUbicacion = sb.instantiateViewControllerWithIdentifier("UbicacionViewController") as! UbicacionViewController
        let _ = self.controladorUbicacion.view
    }
    
    override func tearDown() {
        super.tearDown()
    }
    
    // Prueba cambiar el kilometro en interfaz
    func testCambiarKilometro() {
        let sender = UIStepper()
        sender.value = 55
        self.controladorUbicacion.cambiarKilometro(sender)
        XCTAssertEqual(Datos.kilometros, Int(sender.value).description)
    }
    
    // Prueba obtener los estados del servicio
    func testActualizaEstados() {
        self.controladorUbicacion.actualizaEstados()
        
        let expectativa = expectationWithDescription("Estados obtenidos")
        
        self.performBlock({
            XCTAssertNotNil(self.controladorUbicacion.informacionSelectorEstados)
            XCTAssertEqual(self.controladorUbicacion.informacionSelectorEstados[1]["estado"], "Aguascalientes")
            expectativa.fulfill()
            }, afterDelay: 3)
        
        self.waitForExpectationsWithTimeout(3.5) { (error) -> Void in
            print(error)
        }
    }
    
    // Prueba obtener los municipios del servicio
    func testActualizaMunicipios() {
        let estado = "2"
        let municipio = "Mexicali"
        self.controladorUbicacion.actualizaMunicipios(estado)
        
        let expectativa = expectationWithDescription("Municipios obtenidos")
        
        self.performBlock({
            XCTAssertNotNil(self.controladorUbicacion.informacionSelectorMunicipiosOrigen)
            XCTAssertNotNil(self.controladorUbicacion.informacionSelectorMunicipiosDestino)
            print(self.controladorUbicacion.informacionSelectorMunicipiosOrigen[1]["municipio"])
            XCTAssertEqual(self.controladorUbicacion.informacionSelectorMunicipiosOrigen[1]["municipio"], municipio)
            XCTAssertEqual(self.controladorUbicacion.informacionSelectorMunicipiosDestino[1]["municipio"], municipio)
            expectativa.fulfill()
            }, afterDelay: 2)
        
        self.waitForExpectationsWithTimeout(2.5) { (error) -> Void in
            print(error)
        }
    }
    
    func testObtenerEstadosMunicipiosDeMemoria() {
        XCTAssertNil(self.controladorUbicacion.estados)
        XCTAssertNil(self.controladorUbicacion.estadosMunicipios)
        
        self.controladorUbicacion.obtenerEstadosMunicipiosDeMemoria()
        
        XCTAssertNotNil(self.controladorUbicacion.estados)
        XCTAssertNotNil(self.controladorUbicacion.estadosMunicipios)
        XCTAssertEqual(self.controladorUbicacion.estados["1"], "Aguascalientes")
        let estado = self.controladorUbicacion.estadosMunicipios["1"]  as! [String: String]
        XCTAssertEqual(estado["1"], "Aguascalientes")
    }
    
    func testActualizaEstadosDeMemoria() {
        let estado = "Aguascalientes"
        self.controladorUbicacion.estados = ["1": estado, "2": "Baja California Norte"]
        
        self.controladorUbicacion.actualizaEstadosDeMemoria()
        
        XCTAssertNotNil(self.controladorUbicacion.informacionSelectorEstados)
        XCTAssertEqual(self.controladorUbicacion.informacionSelectorEstados[0]["estado"], estado)
    }
    
    func testActualizaMunicipiosDeMemoria() {
        let estado = "1"
        let municipio = "Aguascalientes"
        self.controladorUbicacion.estadosMunicipios = [estado: ["1": municipio]]
        
        self.controladorUbicacion.actualizaMunicipiosDeMemoria(estado)
        
        XCTAssertEqual(self.controladorUbicacion.informacionSelectorMunicipiosOrigen[0]["municipio"], municipio)
        XCTAssertEqual(self.controladorUbicacion.informacionSelectorMunicipiosDestino[0]["municipio"], municipio)
    }
    
    // Ejecuta un bloque de codigo despues del tiempo dado
    func performBlock(block:() -> Void, afterDelay delay:NSTimeInterval){
        dispatch_after(dispatch_time(DISPATCH_TIME_NOW, Int64(delay * Double(NSEC_PER_SEC))), dispatch_get_main_queue(), block)
    }
}
