package com.example.george.ommfcm;

import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.provider.MediaStore;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Toast;

public class ActividadPrincipal extends AppCompatActivity {

    private static final int RESULT_LOAD = 1;
    private static boolean tieneCoordenadas = true;
    private String rutaImagen;

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
        // Crear intent para abrir la aplicación de galería
        Intent galleryIntent = new Intent(Intent.ACTION_PICK,
                android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
        // Empezar intent
        startActivityForResult(galleryIntent, RESULT_LOAD);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        try {
            // Si la imagen se escoge de la galeria
            if (requestCode == RESULT_LOAD && resultCode == RESULT_OK && null != data) {

                // Obtener la información de la imagen
                Uri selectedImage = data.getData();
                String[] informacion_imagen = { MediaStore.Images.Media.DATA };

                // Obtener el cursor
                Cursor cursor = getContentResolver().query(selectedImage,
                        informacion_imagen, null, null, null);
                // Moverse al primer elemento
                cursor.moveToFirst();

                int indice_columna = cursor.getColumnIndex(informacion_imagen[0]);
                rutaImagen = cursor.getString(indice_columna);
                cursor.close();
                if (tieneCoordenadas) {
                    iniciarVistaPrevia();
                }else{
                    iniciarFormulario();
                }
            } else {
                Toast.makeText(this, "Hey pick your image first",
                        Toast.LENGTH_LONG).show();
            }
        } catch (Exception e) {
            Toast.makeText(this, "Something went embrassing", Toast.LENGTH_LONG)
                    .show();
        }

    }

    private void iniciarVistaPrevia() {
        Intent intentVistaPrevia = new Intent(ActividadPrincipal.this, ActividadVistaPrevia.class);
        intentVistaPrevia.putExtra("ruta_imagen", rutaImagen);
        // intentVistaPrevia.putExtra("latitud", coord.getLatitud());
        // intentVistaPrevia.putExtra("longitud", coord.getLongitud());
        startActivity(intentVistaPrevia);
    }

    private void iniciarFormulario() {
        Intent intentFormulario = new Intent(ActividadPrincipal.this, ActividadFormulario.class);
        intentFormulario.putExtra("ruta_imagen", rutaImagen);
        startActivity(intentFormulario);
    }


    public Coordenadas obtener_coordenadas_actuales(){
        return null;
    }

    public Coordenadas obtener_coordenadas_foto(){
        return null;
    }

    private class Coordenadas{
        private int latitud;
        private int longitud;

        Coordenadas(int lat, int lon){
            this.latitud = lat;
            this.longitud = lon;
        }

        public int getLatitude(){
            return this.latitud;
        }

        public int getLongitude(){
            return this.longitud;
        }
    }
}
