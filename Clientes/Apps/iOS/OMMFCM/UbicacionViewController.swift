//
//  UbicacionViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 22/09/15.
//  Copyright © 2015 imt. All rights reserved.
//

import UIKit
import Foundation

class UbicacionViewController: UIViewController, UITextFieldDelegate, UITableViewDataSource, UITableViewDelegate
{
    // Info actual obtenida
    var informacionSelectorMunicipiosOrigen: [[String: String]] = [[:]]
    var informacionSelectorMunicipiosDestino: [[String: String]] = [[:]]
    var informacionSelectorEstados: [[String: String]]  = [[:]]
    
    // Info filtrada para autocompletar
    var informacionFiltradaEstados: [[String: String]] = [[:]]
    var informacionFiltradaMunicipios: [[String: String]] = [[:]]
    
    var campoDeTextoActual: Int?
    var tecladoEnPantalla: Bool = false
    
    @IBOutlet weak var estadoOrigen: UITextField!
    @IBOutlet weak var municipioOrigen: UITextField!
    @IBOutlet weak var estadoDestino: UITextField!
    @IBOutlet weak var municipioDestino: UITextField!
    
    @IBOutlet weak var opciones: UITableView!
    
    // Unwind segue, para regresar aqui
    @IBAction func regresaAUbicacion(segue: UIStoryboardSegue) {}
    
    override func viewDidLoad()
    {
        super.viewDidLoad()
        
        NSNotificationCenter.defaultCenter().addObserver(self, selector: Selector("keyboardWillShow:"), name:UIKeyboardWillShowNotification, object: self.view.window)
        NSNotificationCenter.defaultCenter().addObserver(self, selector: Selector("keyboardWillHide:"), name:UIKeyboardWillHideNotification, object: self.view.window)
        
        //self.cargaPrimerosEstadosMunicipiosDeMemoria() // CargaMemoria
        self.actualizaEstados()
        
        self.estadoOrigen.delegate = self
        self.municipioOrigen.delegate = self
        self.estadoDestino.delegate = self
        self.municipioDestino.delegate = self
        
        self.opciones.delegate = self
        self.opciones.dataSource = self
    }
    
    override func viewWillDisappear(animated: Bool) {
        NSNotificationCenter.defaultCenter().removeObserver(self, name: UIKeyboardWillShowNotification, object: self.view.window)
        NSNotificationCenter.defaultCenter().removeObserver(self, name: UIKeyboardWillHideNotification, object: self.view.window)
    }
    
    func actualizaEstados()
    {
        // URL del servicio y objeto sesion
        let urlServicio = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/estados"
        let url = NSURL(string: urlServicio)
        let sesion = NSURLSession(configuration: NSURLSessionConfiguration.defaultSessionConfiguration())
        
        // solicitud
        let request = NSMutableURLRequest(URL: url!)
        request.HTTPMethod = "GET"
        
        sesion.dataTaskWithRequest(request, completionHandler:
            {(data: NSData?, resp: NSURLResponse?, error: NSError?) in
                let result = try? NSJSONSerialization.JSONObjectWithData(data!, options: .AllowFragments)
                if (resp as! NSHTTPURLResponse).statusCode == 200
                {
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
                }
                else if (resp as! NSHTTPURLResponse).statusCode == 500
                {
                    print("error estados")
                }
        }).resume()
    }
    
    func actualizaMunicipios(estado: String, selector: Int? = nil)
    {
        // URL del servicio y objeto sesion
        let url = NSURL(string: "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/municipios?estado=" + estado)
        let sesion = NSURLSession(configuration: NSURLSessionConfiguration.defaultSessionConfiguration())
        
        // solicitud
        let request = NSMutableURLRequest(URL: url!)
        request.HTTPMethod = "GET"
        
        if selector == nil
        {
            self.actualizaMunicipios(estado, selector: 2)
            self.actualizaMunicipios(estado, selector: 4)
        }
        else if selector == 2
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
                        }
                    }
                    else if (resp as! NSHTTPURLResponse).statusCode == 500
                    {
                        print("error municipios 0")
                    }
            }).resume()
        }
        else if selector == 4
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
                        }
                    }
                    else if (resp as! NSHTTPURLResponse).statusCode == 500
                    {
                        print("error municipios 1")
                    }
            }).resume()
        }
    }
    
    func filtraEstadosCon(texto: String)
    {
        self.informacionFiltradaEstados = []
        for estados in self.informacionSelectorEstados
        {
            if let estado = estados["estado"]
            {
                if estado.localizedCaseInsensitiveContainsString(texto)
                {
                    self.informacionFiltradaEstados.append(estados)
                }
            }
        }
    }
    
    func filtraMunicipiosCon(texto: String, campo: Int)
    {
        self.informacionFiltradaMunicipios = []
        if campo == 2
        {
            for municipios in self.informacionSelectorMunicipiosOrigen
            {
                if let municipio = municipios["municipio"]
                {
                    if municipio.localizedCaseInsensitiveContainsString(texto)
                    {
                        self.informacionFiltradaMunicipios.append(municipios)
                    }
                }
            }
        }
        else
        {
            for municipios in self.informacionSelectorMunicipiosDestino
            {
                if let municipio = municipios["municipio"]
                {
                    if municipio.localizedCaseInsensitiveContainsString(texto)
                    {
                        self.informacionFiltradaMunicipios.append(municipios)
                    }
                }
            }
        }
    }
    
    func moverOpcionesA(posicion: CGFloat) {
        self.opciones.frame.origin = CGPoint(x: self.opciones.frame.origin.x, y: posicion)
    }
    
    // UITextField Delegate
    func textFieldDidBeginEditing(textField: UITextField)
    {
        self.campoDeTextoActual = textField.tag
        self.moverOpcionesA(self.view.convertPoint(textField.frame.origin, fromView: textField.superview).y + textField.frame.size.height)
    }
    
    func textFieldDidEndEditing(textField: UITextField)
    {
        self.opciones.hidden = true
        self.campoDeTextoActual = nil
    }
    
    func textField(textField: UITextField, shouldChangeCharactersInRange range: NSRange, replacementString string: String) -> Bool
    {
        if let texto = textField.text
        {
            switch textField.tag
            {
            case 1, 3:
                self.filtraEstadosCon(texto + string)
                
            case 2, 4:
                self.filtraMunicipiosCon(texto + string, campo: textField.tag)
                
            default:
                break
            }
            self.opciones.reloadData()
            self.opciones.hidden = false
        }
        
        return true
    }
    
    func textFieldShouldReturn(textField: UITextField) -> Bool
    {
        textField.resignFirstResponder()
        return true
    }
    
    // UITableView Delegate
    func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath)
    {
        if self.campoDeTextoActual != nil
        {
            switch self.campoDeTextoActual!
            {
            case 1:
                self.estadoOrigen.text = self.informacionFiltradaEstados[indexPath.row]["estado"]
                self.actualizaMunicipios(self.informacionFiltradaEstados[indexPath.row]["idEstado"]!, selector: 2)
                
            case 2:
                self.municipioOrigen.text = self.informacionFiltradaMunicipios[indexPath.row]["municipio"]
                
            case 3:
                self.estadoDestino.text = self.informacionFiltradaEstados[indexPath.row]["estado"]
                self.actualizaMunicipios(self.informacionFiltradaEstados[indexPath.row]["idEstado"]!, selector: 4)
                
            case 4:
                self.municipioDestino.text = self.informacionFiltradaMunicipios[indexPath.row]["municipio"]
                
            default:
                break
            }
        }
        self.opciones.hidden = true
    }
    
    // UITableView Data Source
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int
    {
        if self.campoDeTextoActual != nil
        {
            switch self.campoDeTextoActual!
            {
            case 1, 3:
                return self.informacionFiltradaEstados.count
                
            case 2, 4:
                return self.informacionFiltradaMunicipios.count
                
            default:
                break
            }
        }
        return 0
    }
    
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell
    {
        let celda = tableView.dequeueReusableCellWithIdentifier("prototipo")
        
        if self.campoDeTextoActual != nil
        {
            switch self.campoDeTextoActual!
            {
            case 1, 3:
                celda?.textLabel?.text = self.informacionFiltradaEstados[indexPath.row]["estado"]
                
            case 2, 4:
                celda?.textLabel?.text = self.informacionFiltradaMunicipios[indexPath.row]["municipio"]
                
            default:
                break
            }
        }
        return celda!
    }
    
    @IBAction func botonContinuar(sender: UIButton)
    {
        if self.municipioOrigen.text!.characters.count > 0 && self.municipioDestino.text!.characters.count > 0
        {
            self.performSegueWithIdentifier("verMapa", sender: self)
        }
        else
        {
            let aviso = UIAlertController(title: "Error: Origen y/o Destino", message: "Porfavor escribe un origen y destino", preferredStyle: UIAlertControllerStyle.Alert)
            let accion = UIAlertAction(title: "Aceptar", style: UIAlertActionStyle.Default, handler: { _ in })
            aviso.addAction(accion)
            self.presentViewController(aviso, animated: true, completion: {})
        }
    }
    
    func keyboardWillHide(sender: NSNotification)
    {
        if self.campoDeTextoActual == 3 || self.campoDeTextoActual == 4
        {
            let userInfo: [NSObject : AnyObject] = sender.userInfo!
            let keyboardSize: CGSize = userInfo[UIKeyboardFrameBeginUserInfoKey]!.CGRectValue.size
            self.view.frame.origin.y += keyboardSize.height
        }
        self.tecladoEnPantalla = false
    }
    
    func keyboardWillShow(sender: NSNotification)
    {
        let userInfo: [NSObject : AnyObject] = sender.userInfo!
        let keyboardSize: CGSize = userInfo[UIKeyboardFrameBeginUserInfoKey]!.CGRectValue.size
        let offset: CGSize = userInfo[UIKeyboardFrameEndUserInfoKey]!.CGRectValue.size
        
        if (self.campoDeTextoActual == 3 || self.campoDeTextoActual == 4) && !self.tecladoEnPantalla
        {
            self.tecladoEnPantalla = true
            if keyboardSize.height == offset.height
            {
                UIView.animateWithDuration(0.1, animations: { () -> Void in
                    self.view.frame.origin.y -= keyboardSize.height
                })
            }
            else
            {
                UIView.animateWithDuration(0.1, animations: { () -> Void in
                    self.view.frame.origin.y += keyboardSize.height - offset.height
                })
            }
        }
    }
    
    @IBAction func tocoPantalla(sender: UITapGestureRecognizer) {
        if self.campoDeTextoActual != nil {
            if self.opciones.hidden {
                self.view.viewWithTag(self.campoDeTextoActual!)?.resignFirstResponder()
            }
        }
    }
    
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?)
    {
        let controladorDestino = segue.destinationViewController as! MapaViewController
        controladorDestino.nombreOrigen = self.municipioOrigen.text! + " " + self.estadoOrigen.text!
        controladorDestino.nombreDestino = self.municipioDestino.text! + " " + self.estadoDestino.text!
    }
}