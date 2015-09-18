package com.example.george.ommfcm;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;

public class ActividadPrincipal extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_principal);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_actividad_principal, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    public void tomar_foto(View view){

    }

    public void escoger_foto_galeria(View view){

    }

    public Coordenadas obtener_coordenadas_actuales(){
        return null;
    }

    public Coordenadas obtener_coordenadas_foto(){
        return null;
    }

    private class Coordenadas{
        private int latitude;
        private int longitude;

        Coordenadas(int lat, int lon){
            this.latitude = lat;
            this.longitude = lon;
        }

        public int getLatitude(){
            return this.latitude;
        }

        public int getLongitude(){
            return this.longitude;
        }
    }
}
