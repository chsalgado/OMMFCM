package com.example.george.ommfcm;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Matrix;
import android.graphics.Point;
import android.media.ExifInterface;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.Projection;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.loopj.android.http.*;

import org.apache.http.Header;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.nio.charset.StandardCharsets;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;


public class ActividadVistaPrevia extends AppCompatActivity {

    ProgressDialog prDialog; // Variable del dialogo de progreso al realizar la operacion de comrpesion y subida de imagne
    String imagenBase64; // Variable donde se va a guardar la imagen en formato string base 64
    RequestParams params = new RequestParams(); // Variable para agregar los parametros que se envian en la llamada http
    String imgRuta; // Variable donde se guarda la ruta de la imagen obtenida de la vista previa
    private double latitud = 0.0;
    private double longitud = 0.0;
    private static String rutaServidor= "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/incidentes"; // Ruta del servidor donde se sube la imagen
    private String lastimgdatetime;
    private GoogleMap gMap;
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
        gMap = ((MapFragment)getFragmentManager().findFragmentById(R.id.map)).getMap();

        if(intentInfo.hasExtra("ruta_imagen")) { // Verificar que la variable existe
            ImageView imgIncidente = (ImageView) findViewById(R.id.img_incidente); // Variable de la ImageView
            imgRuta = intentInfo.getStringExtra("ruta_imagen"); // Obtener ruta de la imegen
            this.latitud = intentInfo.getDoubleExtra("latitud", 0.0);
            this.longitud = intentInfo.getDoubleExtra("longitud", 0.0);

            crearMarcadorMapa();

            Bitmap bmp = decodeFile(imgRuta); // Decodificar imagen
            bmp = resize(bmp, 1000, 1000);
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

    public void crearMarcadorMapa(){

        LatLng selected_point = new LatLng(this.latitud, this.longitud);
        final MarkerOptions marcador = new MarkerOptions();
        marcador.position(selected_point);
        marcador.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_RED));
        gMap.addMarker(marcador);
        gMap.moveCamera(CameraUpdateFactory.newLatLngZoom(selected_point, 10.0f));
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
                if(lastimgdatetime==null){
                    params.put("fecha", getDateTime()); // Agregar fecha a los parametros
                }else {
                    params.put("fecha", lastimgdatetime); // Agregar fecha a los parametros version 2
                }
                params.put("long", longitud); // Agregar longitud a los parametros
                params.put("lat", latitud); // Agregar latitud a los parametros
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
                Intent intentConfirm = new Intent(ActividadVistaPrevia.this, ActividadConfirmacion.class); // Crear nueva accion para mostrar la vista de confirmacion
                startActivity(intentConfirm);
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                prDialog.hide(); // Esconder el dialogo de progreso

                Log.d("ActividadVistaPrevia", "Error: " + error.toString());

                if (statusCode == 404) {
                    Toast.makeText(getApplicationContext(),
                            "Requested resource not found",
                            Toast.LENGTH_LONG).show(); // Mostrar error 400
                } else if (statusCode == 500) {
                    Toast.makeText(getApplicationContext(),
                            "Lo sentimos hubo problemas con el servidor, favor de intentarlo de nuevo",
                            Toast.LENGTH_LONG).show(); // Mostrar error 500
                } else {
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

    private String getDateTime() {
        DateFormat dateFormat = new SimpleDateFormat("yyyy/MM/dd HH:mm:ss");
        Date date = new Date();
        return dateFormat.format(date);
    }

    private Bitmap decodeFile(String path) {//you can provide file path here
        int orientation;
        try {
            if (path == null) {
                return null;
            }
            // decode image size
            BitmapFactory.Options o = new BitmapFactory.Options();
            o.inJustDecodeBounds = true;
            // Find the correct scale value. It should be the power of 2.
            final int REQUIRED_SIZE = 70;
            int width_tmp = o.outWidth, height_tmp = o.outHeight;
            int scale = 0;
            while (true) {
                if (width_tmp / 2 < REQUIRED_SIZE
                        || height_tmp / 2 < REQUIRED_SIZE)
                    break;
                width_tmp /= 2;
                height_tmp /= 2;
                scale++;
            }
            // decode with inSampleSize
            BitmapFactory.Options o2 = new BitmapFactory.Options();
            o2.inSampleSize = scale;
            Bitmap bm = BitmapFactory.decodeFile(path, o2);
            Bitmap bitmap = bm;

            ExifInterface exif = new ExifInterface(path);
            lastimgdatetime =  exif.getAttribute(ExifInterface.TAG_DATETIME);//Obtiene la fecha de creacion de la imagen a subir

            orientation = exif.getAttributeInt(ExifInterface.TAG_ORIENTATION, 1);

            Log.e("ExifInteface .........", "rotation =" + orientation);

            //exif.setAttribute(ExifInterface.ORIENTATION_ROTATE_90, 90);

            Log.e("orientation", "" + orientation);
            Matrix m = new Matrix();

            if ((orientation == ExifInterface.ORIENTATION_ROTATE_180)) {
                m.postRotate(180);
                //m.postScale((float) bm.getWidth(), (float) bm.getHeight());
                // if(m.preRotate(90)){
                Log.e("in orientation", "" + orientation);
                bitmap = Bitmap.createBitmap(bm, 0, 0, bm.getWidth(),bm.getHeight(), m, true);
                return bitmap;
            } else if (orientation == ExifInterface.ORIENTATION_ROTATE_90) {
                m.postRotate(90);
                Log.e("in orientation", "" + orientation);
                bitmap = Bitmap.createBitmap(bm, 0, 0, bm.getWidth(),bm.getHeight(), m, true);
                return bitmap;
            }
            else if (orientation == ExifInterface.ORIENTATION_ROTATE_270) {
                m.postRotate(270);
                Log.e("in orientation", "" + orientation);
                bitmap = Bitmap.createBitmap(bm, 0, 0, bm.getWidth(),bm.getHeight(), m, true);
                return bitmap;
            }
            return bitmap;
        } catch (Exception e) {
            return null;
        }
    }

    private  Bitmap resize(Bitmap image, int maxWidth, int maxHeight) {
        if (maxHeight > 0 && maxWidth > 0) {
            int width = image.getWidth();
            int height = image.getHeight();
            float ratioBitmap = (float) width / (float) height;
            float ratioMax = (float) maxWidth / (float) maxHeight;

            int finalWidth = maxWidth;
            int finalHeight = maxHeight;
            if (ratioMax > 1) {
                finalWidth = (int) ((float)maxHeight * ratioBitmap);
            } else {
                finalHeight = (int) ((float)maxWidth / ratioBitmap);
            }
            image = Bitmap.createScaledBitmap(image, finalWidth, finalHeight, true);
            return image;
        } else {
            return image;
        }
    }
}

