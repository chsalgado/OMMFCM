//
//  UbicacionViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 22/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

import UIKit

class UbicacionViewController: UIViewController, UIPickerViewDataSource, UIPickerViewDelegate
{
    var estados: [String: String]! // De memoria
    var estadosMunicipios: [String: NSDictionary]!  // De memoria
    
    // Info actual mostrandose en seleccion
    var informacionSelectorMunicipiosOrigen: [[String: String]] = [[:]]
    var informacionSelectorMunicipiosDestino: [[String: String]] = [[:]]
    var informacionSelectorEstados: [[String: String]]  = [[:]]
    
    @IBOutlet weak var selectorOrigen: UIPickerView!
    @IBOutlet weak var selectorDestino: UIPickerView!
    
    @IBOutlet weak var kilometro: UILabel!
    
    @IBAction func cambiarKilometro(sender: UIStepper)
    {
        let km = Int(sender.value).description
        kilometro.text = km
        Datos.kilometros = km
    }
    
    // Unwind segue, para regresar aqui
    @IBAction func regresaAUbicacion(segue: UIStoryboardSegue) {}
    
    override func viewDidLoad()
    {
        super.viewDidLoad()
        
        self.cargaPrimerosEstadosMunicipiosDeMemoria()
        self.actualizaEstados()
        
        self.selectorOrigen.delegate = self
        self.selectorOrigen.dataSource = self
        self.selectorDestino.delegate = self
        self.selectorDestino.dataSource = self
    }
    
    func actualizaEstados()
    {
        // URL del servicio y objeto sesion
        let url = NSURL(string: "http://jorgegonzac-001-site1.hostbuddy.com/public_html/index.php/api/estados")
        let sesion = NSURLSession(configuration: NSURLSessionConfiguration.defaultSessionConfiguration())
        
        // solicitud
        let request = NSMutableURLRequest(URL: url!)
        request.HTTPMethod = "GET"
        
        sesion.dataTaskWithRequest(request, completionHandler:
            {(data: NSData?, resp: NSURLResponse?, error: NSError?) in
                let result = try? NSJSONSerialization.JSONObjectWithData(data!, options: .AllowFragments)
                if (resp as! NSHTTPURLResponse).statusCode == 200
                {
                    print("correcto")
                    let res = result as! Dictionary<String, AnyObject>
                    if let estados = res["estados"] as? [Dictionary<String, AnyObject>]
                    {
                        self.informacionSelectorEstados = [[:]]
                        var infoEstado = [String: String]()
                        
                        for estado in estados
                        {
                            let idEstado = (estado["id_estado"] as! Int).description
                            let estadoNombre = estado["estado"] as? String
                            
                            infoEstado["idEstado"] = idEstado
                            infoEstado["estado"] = estadoNombre
                            
                            self.informacionSelectorEstados.append(infoEstado)
                        }
                    }
                    self.informacionSelectorEstados.removeFirst()
                    self.selectorOrigen.reloadComponent(0)
                    self.selectorDestino.reloadComponent(0)
                    self.actualizaMunicipios(self.informacionSelectorEstados.first!["idEstado"]!)
                }
                else if (resp as! NSHTTPURLResponse).statusCode == 500
                {
                    print("error estados")
                    self.actualizaEstados()
                }
        }).resume()
    }
    
    func actualizaMunicipios(estado: String, selector: Int? = nil)
    {
        // URL del servicio y objeto sesion
        let url = NSURL(string: "http://jorgegonzac-001-site1.hostbuddy.com/public_html/index.php/api/municipios?estado=" + estado)
        let sesion = NSURLSession(configuration: NSURLSessionConfiguration.defaultSessionConfiguration())
        
        // solicitud
        let request = NSMutableURLRequest(URL: url!)
        request.HTTPMethod = "GET"
        
        if selector == nil
        {
            self.actualizaMunicipios(estado, selector: 0)
            self.actualizaMunicipios(estado, selector: 1)
        }
        else if selector == 0
        {
            sesion.dataTaskWithRequest(request, completionHandler:
                {(data: NSData?, resp: NSURLResponse?, error: NSError?) in
                    let result = try? NSJSONSerialization.JSONObjectWithData(data!, options: .AllowFragments)
                    if (resp as! NSHTTPURLResponse).statusCode == 200
                    {
                        let res = result as! Dictionary<String, AnyObject>
                        if let municipios = res["municipios"] as? [Dictionary<String, AnyObject>]
                        {
                            self.informacionSelectorMunicipiosOrigen = [[:]]
                            var infoMunicipios = [String: String]()
                            for municipio in municipios
                            {
                                infoMunicipios["idMunicipio"] = (municipio["id_municipio"] as! Int).description
                                infoMunicipios["municipio"] = municipio["nombre_municipio"] as? String
                                infoMunicipios["idEstado"] = (municipio["estado"] as! Int).description
                                self.informacionSelectorMunicipiosOrigen.append(infoMunicipios)
                            }
                            self.informacionSelectorMunicipiosOrigen.removeFirst()
                            self.selectorOrigen.reloadComponent(1)
                        }
                    }
                    else if (resp as! NSHTTPURLResponse).statusCode == 500
                    {
                        self.actualizaMunicipios(estado, selector: selector)
                    }
            }).resume()
        }
        else if selector == 1
        {
            sesion.dataTaskWithRequest(request, completionHandler:
                {(data: NSData?, resp: NSURLResponse?, error: NSError?) in
                    let result = try? NSJSONSerialization.JSONObjectWithData(data!, options: .AllowFragments)
                    if (resp as! NSHTTPURLResponse).statusCode == 200
                    {
                        let res = result as! Dictionary<String, AnyObject>
                        if let municipios = res["municipios"] as? [Dictionary<String, AnyObject>]
                        {
                            self.informacionSelectorMunicipiosDestino = [[:]]
                            var infoMunicipios = [String: String]()
                            for municipio in municipios
                            {
                                infoMunicipios["idMunicipio"] = (municipio["id_municipio"] as! Int).description
                                infoMunicipios["municipio"] = municipio["nombre_municipio"] as? String
                                infoMunicipios["idEstado"] = (municipio["estado"] as! Int).description
                                self.informacionSelectorMunicipiosDestino.append(infoMunicipios)
                            }
                            self.informacionSelectorMunicipiosDestino.removeFirst()
                            self.selectorDestino.reloadComponent(1)
                        }
                    }
                    else if (resp as! NSHTTPURLResponse).statusCode == 500
                    {
                        self.actualizaMunicipios(estado, selector: selector)
                    }
            }).resume()
        }
    }
    
    // Funciones de DataSource delegate
    func numberOfComponentsInPickerView(pickerView: UIPickerView) -> Int
    {
        return 2
    }
    
    func pickerView(pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int
    {
        if component == 0
        {
            return self.informacionSelectorEstados.count
        }
        else
        {
            if pickerView.tag == 0
            {
                return self.informacionSelectorMunicipiosOrigen.count
            }
            else
            {
                return self.informacionSelectorMunicipiosDestino.count
            }
        }
    }
    
    // Funciones de Picker delegate
    func pickerView(pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int)
    {
        if component == 0
        {
            let idEstado = self.informacionSelectorEstados[row]["idEstado"]
            self.actualizaMunicipios(idEstado!, selector: pickerView.tag)
        }
        else
        {
            if pickerView.tag == 0
            {
                Datos.municipioOrigen = self.informacionSelectorMunicipiosOrigen[row]["idMunicipio"]
                Datos.municipioOrigenTexto = self.informacionSelectorMunicipiosOrigen[row]["municipio"]
            }
            else
            {
                Datos.municipioDestino = self.informacionSelectorMunicipiosDestino[row]["idMunicipio"]
                Datos.municipioDestinoTexto = self.informacionSelectorMunicipiosDestino[row]["municipio"]
            }
        }
    }
    
    func pickerView(pickerView: UIPickerView, viewForRow row: Int, forComponent component: Int, reusingView view: UIView?) -> UIView
    {
        var pickerLabel = view as? UILabel
        if pickerLabel == nil
        {
            pickerLabel = UILabel()
            pickerLabel!.font = UIFont(name: "System", size: 15)
            pickerLabel!.textAlignment = NSTextAlignment.Center
        }
        
        if component == 0   // Estados
        {
            pickerLabel?.text = self.informacionSelectorEstados[row]["estado"]
        }
        else
        {
            if pickerView.tag == 0  // Origen
            {
                pickerLabel?.text = self.informacionSelectorMunicipiosOrigen[row]["municipio"]
            }
            else
            {
                pickerLabel?.text = self.informacionSelectorMunicipiosDestino[row]["municipio"]
            }
        }
        return pickerLabel!
    }
    
    // Estados y municipios de memoria
    func cargaPrimerosEstadosMunicipiosDeMemoria()
    {
        self.obtenerEstadosMunicipiosDeMemoria()
        self.actualizaEstadosDeMemoria()
        self.actualizaMunicipiosDeMemoria(self.informacionSelectorEstados.first!["idEstado"]!)// obtengo el estado del primer municipio
    }
    
    func obtenerEstadosMunicipiosDeMemoria()
    {
        var path = NSBundle.mainBundle().pathForResource("estados", ofType: "json")
        var data = NSData(contentsOfFile: path!)
        var result = try? NSJSONSerialization.JSONObjectWithData(data!, options: .AllowFragments)
        
        if let dic = result as? NSDictionary
        {
            if let estadosLeidos = dic["estados"] as? NSDictionary
            {
                self.estados = estadosLeidos as! [String : String]
            }
        }
        
        path = NSBundle.mainBundle().pathForResource("municipios", ofType: "json")
        data = NSData(contentsOfFile: path!)
        result = try? NSJSONSerialization.JSONObjectWithData(data!, options: .AllowFragments)
        
        if let dic = result as? NSDictionary
        {
            if let estadosLeidos = dic["estados"] as? NSDictionary
            {
                self.estadosMunicipios = estadosLeidos as! [String: NSDictionary]
            }
        }
    }
    
    func actualizaEstadosDeMemoria()
    {
        self.informacionSelectorEstados = [[:]]
        
        var infoEstado = [String: String]()
        
        for (idEstado, nomEstado) in self.estados
        {
            infoEstado["idEstado"] = idEstado
            infoEstado["estado"] = nomEstado
            self.informacionSelectorEstados.append(infoEstado)
        }
        self.informacionSelectorEstados.removeFirst()
        self.informacionSelectorEstados.sortInPlace({return $0["estado"] < $1["estado"]})
    }
    
    func actualizaMunicipiosDeMemoria(estado: String, selector: Int? = nil)
    {
        if selector == nil
        {
            self.actualizaMunicipiosDeMemoria(estado, selector: 0)
            self.actualizaMunicipiosDeMemoria(estado, selector: 1)
        }
        else if selector == 0
        {
            self.informacionSelectorMunicipiosOrigen = [[:]]
            
            // obtengo los municipios del estado con su llave
            let municipiosDeEstado = self.estadosMunicipios[estado] as! [String: String]
            
            var infoMunicipios: [String: String] = [:]
            for (idMunicipio, nombreMunicipio) in municipiosDeEstado {
                infoMunicipios["idMunicipio"] = idMunicipio
                infoMunicipios["municipio"] = nombreMunicipio
                infoMunicipios["idEstado"] = estado
                self.informacionSelectorMunicipiosOrigen.append(infoMunicipios)
            }
            self.informacionSelectorMunicipiosOrigen.removeFirst()
            self.selectorOrigen.reloadComponent(1)
        }
        else if selector == 1
        {
            self.informacionSelectorMunicipiosDestino = [[:]]
            
            // obtengo los municipios del estado con su llave
            let municipiosDeEstado = self.estadosMunicipios[estado] as! [String: String]
            
            var infoMunicipios: [String: String] = [:]
            for (idMunicipio, nombreMunicipio) in municipiosDeEstado {
                infoMunicipios["idMunicipio"] = idMunicipio
                infoMunicipios["municipio"] = nombreMunicipio
                infoMunicipios["idEstado"] = estado
                self.informacionSelectorMunicipiosDestino.append(infoMunicipios)
            }
            self.informacionSelectorMunicipiosDestino.removeFirst()
            self.selectorDestino.reloadComponent(1)
        }
    }
}