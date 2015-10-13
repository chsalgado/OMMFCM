package com.example.george.ommfcm;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;

import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;

public class ActividadMapa extends AppCompatActivity {
    private GoogleMap gMap; // Variable para invocar el mapa
    private static final int MAPA = 1000; // Variable que identifica el codigo de la actividad

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_mapa);

        gMap = ((MapFragment)getFragmentManager().findFragmentById(R.id.map)).getMap(); // Creacion del mapa

        // Obtencion de variables de la ventana anterior
        final String origenEstado = getIntent().getStringExtra("OrigenEstado");
        final String origenMunicipio = getIntent().getStringExtra("OrigenMunicipio");
        final String destinoEstado = getIntent().getStringExtra("DestinoEstado");
        final String destinoMunicipio = getIntent().getStringExtra("DestinoMunicipio");

        new RutaMapa(this, gMap, origenEstado, origenMunicipio, destinoEstado, destinoMunicipio).execute(); // Calcular y dibjar ruta en mapa de acuerdo al origen y destino
    }

    /**
     * TODO: Metodo que regresa a la vista anterior y pasa las coordenadas seleccionadas en el mapa
     * @param view
     */
    public void irVistaFormulario(View view){
        this.finishActivity(MAPA);
    }
}
