package com.example.george.ommfcm;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;

import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;

public class ActividadMapa extends AppCompatActivity {
    private GoogleMap gMap;
    private static final int MAPA = 1000;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_mapa);

        gMap = ((MapFragment)getFragmentManager().findFragmentById(R.id.map)).getMap();

        final String origenEstado = getIntent().getStringExtra("OrigenEstado");
        final String origenMunicipio = getIntent().getStringExtra("OrigenMunicipio");
        final String destinoEstado = getIntent().getStringExtra("DestinoEstado");
        final String destinoMunicipio = getIntent().getStringExtra("DestinoMunicipio");

        new RutaMapa(this, gMap, origenEstado, origenMunicipio, destinoEstado, destinoMunicipio).execute();
    }

    public void irVistaFormulario(View view){
        this.finishActivity(MAPA);
    }
}
