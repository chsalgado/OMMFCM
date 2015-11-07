//
//  InformacionViewController.swift
//  OMMFCM
//
//  Created by Juan Hernández López on 05/11/15.
//  Copyright © 2015 imt. All rights reserved.
//

import UIKit

class InformacionViewController: UIViewController {
    
    @IBOutlet weak var textoInformacion: UITextView!
    
    // Acomoda el texto para mostrarlo desde arriba
    override func viewDidLoad() {
        self.textoInformacion.scrollRangeToVisible(NSMakeRange(0, 0))
    }
}
