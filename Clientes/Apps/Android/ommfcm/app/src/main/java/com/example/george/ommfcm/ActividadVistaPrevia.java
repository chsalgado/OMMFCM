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

    ProgressDialog prDialog;
    String imagenBase64;
    RequestParams params = new RequestParams();
    String imgRuta;
    Bitmap bitmap;
    private static int RESULT_LOAD_IMAGE = 1;
    private static String rutaServidor= "http://10.25.108.12:80/imgupload/upload_image.php";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_vista_previa);
        Intent intentInfo = getIntent();
        if(intentInfo.hasExtra("ruta_imagen")) {
            ImageView imgIncidente = (ImageView) findViewById(R.id.img_incidente);
            imgRuta = intentInfo.getStringExtra("ruta_imagen");
            Bitmap bmp = BitmapFactory.decodeFile(imgRuta);
            imgIncidente.setImageBitmap(bmp);
            prDialog = new ProgressDialog(this);
            prDialog.setCancelable(false);
        }

    }


    // Método que se activa cuando se preiona el boton de subir imagen
    public void subirIncidente(View view){
        prDialog.setMessage("Procesando imagen");
        prDialog.show();

        //Convertir imagen a base 64
        convertirImagenABase64();

    }

    private void convertirImagenABase64() {
        new AsyncTask<Void, Void, String>(){
            @Override
            protected String doInBackground(Void... params) {
                BitmapFactory.Options opciones = null;
                opciones = new BitmapFactory.Options();
                opciones.inSampleSize = 3;
                bitmap = BitmapFactory.decodeFile(imgRuta, opciones);
                ByteArrayOutputStream stream = new ByteArrayOutputStream();
                // Compresion de imagen
                bitmap.compress(Bitmap.CompressFormat.JPEG, 50, stream);
                byte[] byte_arr = stream.toByteArray();
                // Convertir imagen a cadena Base 64
                imagenBase64 = Base64.encodeToString(byte_arr, 0);
                return null;
            }
            @Override
            protected void onPostExecute(String msg){
                prDialog.setMessage("Subiendo imagen");
                // Colocar imagen en los parametros de la solicitud http
                params.put("imagen", imagenBase64);
                // Llamar al metodo de subir imagen
                realizarLlamadaHTTP();
            }
        }.execute(null, null, null);
    }

    public void realizarLlamadaHTTP(){
        prDialog.setMessage("Subiendo imagen al servidor");
        AsyncHttpClient client = new AsyncHttpClient();
        client.post(rutaServidor, params, new JsonHttpResponseHandler() {

            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                // Esconder el dialogo de progreso
                prDialog.hide();
                Toast.makeText(getApplicationContext(), "Imagen subida correctamente", Toast.LENGTH_LONG).show();
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                // Esconder el dialogo de progreso
                prDialog.hide();

                Log.d("ActividadVistaPrevia", "Error: " + error.toString());

                if(statusCode == 404){
                    Toast.makeText(getApplicationContext(),
                            "Requested resource not found",
                            Toast.LENGTH_LONG).show();
                }else if(statusCode == 500){
                    Toast.makeText(getApplicationContext(),
                            "Lo sentimos hubo problemas con el servidor, favor de intentarlo de nuevo",
                            Toast.LENGTH_LONG).show();
                }else {
                    Toast.makeText(
                            getApplicationContext(),
                            "Ocurrió un error \n Causas mas comunes: \n" +
                            "1. Se perdio la conexión a internet\n" +
                            "2. El servidor no esta funcionando", Toast.LENGTH_LONG)
                            .show();
                }
            }
        });
    }

    @Override
    protected void onDestroy(){
        super.onDestroy();
        // Desechar dialogo de progreso cuando la aplicacion se cierre
        if(prDialog != null){
            prDialog.dismiss();
        }
    }
}
