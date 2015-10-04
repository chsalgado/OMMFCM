package com.example.george.ommfcm;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageView;
import android.widget.Toast;

import com.loopj.android.http.*;

import org.apache.http.Header;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.nio.charset.StandardCharsets;


public class ActividadVistaPrevia extends AppCompatActivity {

    ProgressDialog prDialog; // Variable del dialogo de progreso al realizar la operacion de comrpesion y subida de imagne
    String imagenBase64; // Variable donde se va a guardar la imagen en formato string base 64
    RequestParams params = new RequestParams(); // Variable para agregar los parametros que se envian en la llamada http
    String imgRuta; // Variable donde se guarda la ruta de la imagen obtenida de la vista previa
    private static String rutaServidor= "http://10.25.108.12/api/OMMFCM/public/api/incidentes"; // Ruta del servidor donde se sube la imagen

    /**
     * Metodo que se llama al crearse la vista por primera vez
     *
     * @param savedInstanceState
     */
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_vista_previa); // Asignar layout de la vista
        Intent intentInfo = getIntent(); // Obtener informacion de la vista pasada

        if(intentInfo.hasExtra("ruta_imagen")) { // Verificar que la variable existe
            ImageView imgIncidente = (ImageView) findViewById(R.id.img_incidente); // Variable de la ImageView
            imgRuta = intentInfo.getStringExtra("ruta_imagen"); // Obtener ruta de la imegen
            Bitmap bmp = BitmapFactory.decodeFile(imgRuta); // Decodificar imagen
            imgIncidente.setImageBitmap(bmp); // Asignar imagen decodificada a imageView
            prDialog = new ProgressDialog(this); // Crear un dialogo para mostrar progreso
            prDialog.setCancelable(false);
        }

    }

    /**
     * Método que se activa cuando se preiona el boton de subir imagen
     *
     * @param view Vista donde se ejecuta el metodo
     */
    public void subirIncidente(View view){
        prDialog.setMessage("Procesando imagen"); // Asignar mensaje al dialogo de progreso
        prDialog.show(); // Mostrar dialogo de progreso

        subirImagen();  //Convertir imagen a base 64

    }

    /**
     * Metodo asyncrono que convierte la imagen a un string base 64 y la sube al servidor
     */
    private void subirImagen() {
        new AsyncTask<Void, Void, String>(){
            /**
             * Metodo que convierte la imagen a base 64 en background
             *
             * @param params
             * @return string con la imagen codificada
             */
            @Override
            protected String doInBackground(Void... params) {
                BitmapFactory.Options opciones = null;
                opciones = new BitmapFactory.Options();
                opciones.inSampleSize = 3;
                Bitmap bitmap = BitmapFactory.decodeFile(imgRuta, opciones);

                ByteArrayOutputStream stream = new ByteArrayOutputStream();
                bitmap.compress(Bitmap.CompressFormat.JPEG, 50, stream); // Comprimir imagen
                byte[] byte_arr = stream.toByteArray(); // Pasar la imagen comprimida a un arreglo de bytes
                imagenBase64 = Base64.encodeToString(byte_arr, 0); // Convertir imagen a cadena Base 64
                return null;
            }

            /**
             * Metodo que se ejecuta al finalizar la conversion
             *
             * @param msg
             */
            @Override
            protected void onPostExecute(String msg){
                prDialog.setMessage("Subiendo imagen");
                params.put("imagen", imagenBase64); // Agregar string de la imagen a los parametros de la llamada HTTP
                params.put("fecha","2014-02-18 15:00:00"); // Agregar fecha a los parametros
                params.put("long", 123.94599969); // Agregar longitud a los parametros
                params.put("lat", 99.99999999); // Agregar latitud a los parametros
                params.put("mpioOrigen", 2140); // Agregar municipio de origen a los parametros
                params.put("mpioDestino", 2402); // Agregar municipio de destino a los parametros
                params.put("km", "79"); // Agregar kilometro a los parametros
                params.put("extension", ".jpg"); // Agregar extension de la imagen a los parametros
                realizarLlamadaHTTP(); // Realizar llamada http para subir la imagen
            }
        }.execute(null, null, null);
    }

    /**
     * Metodo que realiza la llamada HTTP para subir la imagen al servidor
     */
    public void realizarLlamadaHTTP(){
        prDialog.setMessage("Subiendo imagen al servidor");
        AsyncHttpClient client = new AsyncHttpClient();
        client.post(rutaServidor, params, new JsonHttpResponseHandler() {

            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                prDialog.hide(); // Esconder el dialogo de progreso
                Toast.makeText(getApplicationContext(), "Imagen subida correctamente", Toast.LENGTH_LONG).show(); // Mostrar mensaje de operacion exitosa
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                prDialog.hide(); // Esconder el dialogo de progreso

                Log.d("ActividadVistaPrevia", "Error: " + error.toString());

                if(statusCode == 404){
                    Toast.makeText(getApplicationContext(),
                            "Requested resource not found",
                            Toast.LENGTH_LONG).show(); // Mostrar error 400
                }else if(statusCode == 500){
                    Toast.makeText(getApplicationContext(),
                            "Lo sentimos hubo problemas con el servidor, favor de intentarlo de nuevo",
                            Toast.LENGTH_LONG).show(); // Mostrar error 500
                }else {
                    Toast.makeText(
                            getApplicationContext(),
                            "Ocurrió un error \n  Posibles causas: \n" +
                            "1. Se perdio la conexión a internet\n" +
                            "2. El servidor no esta funcionando", Toast.LENGTH_LONG)
                            .show(); // Mostrar error
                }
            }
        });
    }

    /**
     * Metodo que se llama al cerrar la aplicación
     */
    @Override
    protected void onDestroy(){
        super.onDestroy();

        if(prDialog != null){
            prDialog.dismiss(); // Desechar dialogo de progreso cuando la aplicacion se cierre
        }
    }
}
