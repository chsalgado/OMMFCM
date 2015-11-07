package com.example.george.ommfcm;

import android.app.ActionBar;
import android.app.AlertDialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.location.Location;
import android.location.LocationManager;
import android.media.ExifInterface;
import android.net.Uri;
import android.os.Environment;
import android.provider.MediaStore;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.location.LocationServices;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;

public class ActividadPrincipal extends AppCompatActivity implements
        GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener{

    private static final int GALLERY_REQUEST = 1; // Codigo para identificar la llamada a la aplicación de galeria
    private static final int CAM_REQUEST = 2; // Codigo para identificar la llamada a la aplicacion de la camara
    private double latitud = 0; // Variable para guardar latitud
    private double longitud = 0; // Variable para guardar longitud
    private String rutaImagen;
    private Uri uriImagen;

    public GoogleApiClient mGoogleApiClient;

    public Location mLastLocation;

    /**
     * Metodo que se llama cuando se crea la vista por primera vez
     */
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_principal); // Cargar layout


        crearClienteLocalizacion(); // Crear cliente para localizacion

        if(!gpsEstaActivado())
            solicitarActivacionGPS(); // Pedir al usuario la activacion del gps en caso de que se encuentre apagado
    }

    /**
     * Metodo que se llama cada vez que la vista se hace visible para el usuario
     */
    @Override
    protected void onStart() {
        super.onStart();

        if(gpsEstaActivado()) // Comprueba si el gps del dispositivo se encuentra activado
            mGoogleApiClient.connect(); // Conecta con el servicio de ubicación de Google
    }

    /**
     * Metodo que se llama al cambiar a una nueva vista o al cerrar la aplicación
     */
    @Override
    protected void onStop() {
        super.onStop();
        if (mGoogleApiClient.isConnected()) {
            mGoogleApiClient.disconnect(); // Detiene la conexión con el servicio de Google
        }
    }

    /**
     * Una vez que se conectó con los servicios de ubicación de google, obtiene las coordenadas de la posición actual
     *
     * @param connectionHint
     */
    @Override
    public void onConnected(Bundle connectionHint) {
        mLastLocation = LocationServices.FusedLocationApi.getLastLocation(mGoogleApiClient); // Obtiene la última localización conocida del dispositivo
        if (mLastLocation != null) {
            this.latitud = mLastLocation.getLatitude(); // Asignar latitud a variable de clase
            this.longitud = mLastLocation.getLongitude(); // Asignar longitud a variable de clase
            //Log.d("ActividadPrincipal", String.valueOf(mLastLocation.getLatitude()) + " - " + mLastLocation.getLongitude());
        } else {
            //Log.d("ActividadPrincipal", "No se detectaron coordenadas");
        }
    }

    /**
     * Mensaje de error en caso de que la conexión falle
     *
     * @param result
     */
    @Override
    public void onConnectionFailed(ConnectionResult result) {
        //Log.d("ActividadPrincipal", "No se pudieron obtener las coordenadas");
    }

    /**
     * Mensaje de alerta en caso de que la conexión se haya suspendido
     *
     * @param cause
     */
    @Override
    public void onConnectionSuspended(int cause) {
        //Log.d("ActividadPrincipal", "Conexión suspendida");
        //mGoogleApiClient.connect();
    }

    /**
     * Metodo que verifica si el servicio de gps se encuentra activado en el telefono
     *
     * @return true si el gps esta activado, false en caso contrario
     */
    private boolean gpsEstaActivado(){
        LocationManager locationManager;
        String context = Context.LOCATION_SERVICE;
        locationManager = (LocationManager)getSystemService(context); // Obtener el servicio de localizacion

        return locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER); // Verifica si el servicio de localizacion esa disponible
    }

    /**
     * Metodo que crea un nuevo cliente para utilizar los servicios de Google
     */
    public synchronized void crearClienteLocalizacion() {
        try {
            mGoogleApiClient = new GoogleApiClient.Builder(this)
                    .addConnectionCallbacks((GoogleApiClient.ConnectionCallbacks) this)
                    .addOnConnectionFailedListener((GoogleApiClient.OnConnectionFailedListener) this)
                    .addApi(LocationServices.API)
                    .build();
        } catch(NullPointerException npe) {
            throw new NullPointerException();
        } catch(IllegalStateException ise) {
            throw new IllegalStateException();
        }
    }

    /**
     * Metodo que muestra una alerta solicitando la activacion del servicio de ubicación
     */
    public boolean solicitarActivacionGPS() {
        try {
            final AlertDialog.Builder builder = new AlertDialog.Builder(this);
            //Construye la alerta. Se coloca mensaje, botón para confirmación, y cancelación.
            builder.setMessage("El sistema GPS esta desactivado, ¿Desea activarlo?")
                    .setCancelable(false)
                    .setPositiveButton("Si", new DialogInterface.OnClickListener() {
                        public void onClick(@SuppressWarnings("unused") final DialogInterface dialog, @SuppressWarnings("unused") final int id) {
                            startActivity(new Intent(android.provider.Settings.ACTION_LOCATION_SOURCE_SETTINGS));
                        }
                    })
                    .setNegativeButton("No", new DialogInterface.OnClickListener() {
                        public void onClick(final DialogInterface dialog, @SuppressWarnings("unused") final int id) {
                            dialog.cancel();
                        }
                    });

            AlertDialog alert = builder.create(); // Crear alerta
            alert.show(); // Mostrar alerta
            return true;
        } catch (Exception e) {
            return false;
        }
    }

    /**
     * Metodo que llama a la aplicacion de camara del dispositivo para tomar foto
     *
     * @param view Vista donde se va ejecutar este metodo, parametro por defecto que permite
     *             al metodo ser invocado por un boton en el archivo de layout
     */
    public void tomar_foto(View view){
        Intent intentCamara = new Intent(MediaStore.ACTION_IMAGE_CAPTURE); // Crear nueva accion para ejecutar la aplicacion de camara
        File archivoImagen = crearArchivoSalida(); // Crear archivo donde se va a guardar la imagen
        uriImagen = Uri.fromFile(archivoImagen); // Obtener ruta del archivo
        intentCamara.putExtra(MediaStore.EXTRA_OUTPUT, uriImagen); // Pasar ruta de archivo en el intent
        startActivityForResult(intentCamara, CAM_REQUEST); // Inicia la aplicación de camara
    }

    /**
     * Metodo que invoca a la actividad con mas informacion sobre la aplicacion
     * @param view Vista donde se ejecuta el metodo
     */
    public void mostrar_info(View view){
        Intent intentInfo = new Intent(ActividadPrincipal.this, ActividadInfo.class); // Crear nueva accion para mostrar mas informacion

        startActivity(intentInfo); // Inicia la actividad de mas_info
    }

    /**
     * Metodo que invoca a la actividad_precauciones que incluye recomendaciones para el buen uso de la aplicacion
     * @param view Vista donde se ejecuta el metodo
     */
    public void mostrar_precauciones(View view){
        Intent intentPrecauciones = new Intent(ActividadPrincipal.this, ActividadPrecauciones.class); // Crear nueva accion para mostrar mas informacion

        startActivity(intentPrecauciones); // Inicia la actividad de precauciones
    }

    /**
     * Metodo que llama a la aplicacion de galeria del dispositivo para seleccionar una foto
     *
     * @param view Vista donde se va a ejecutar este metodo
     */
    public void escoger_foto_galeria(View view){
        Intent intentGaleria = new Intent(Intent.ACTION_PICK,
                android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI); // Se crea el intent para abrir la aplicación de galería
        startActivityForResult(intentGaleria, GALLERY_REQUEST); // Inicia la aplicación de galeria
    }

    /**
     * Metodo que verifica si una foto tiene coordenadas guardadas en su metadata
     *
     * @param rutaImagen
     * @return true si la imagen tiene coordenadas, false en caso contrario
     */
    public boolean tieneCoordenadasImagen(String rutaImagen){
        try {
            float[] coordenadas = new float[2]; // Variable para guardar las coordenadas de la imagen
            ExifInterface exifInterface = new ExifInterface(rutaImagen); // Crear objeto para leer metadata de imagen
            if(exifInterface.getLatLong(coordenadas)) {
                this.latitud = (double) coordenadas[0];
                this.longitud = (double) coordenadas[1];
                return true;
            }
        } catch (IOException e) {
            Toast.makeText(ActividadPrincipal.this,
                    e.toString(),
                    Toast.LENGTH_LONG).show(); // Mostrar mensaje en caso de error
        }

        return false;
    }

    /**
     * Metodo que obtiene la ruta absoluta de una imagen
     *
     * @param imagenSeleccionada Uri de la imagen
     * @return string con la ruta absoluta de la imagen
     */
    public String obtenerRutaRealUri(Uri imagenSeleccionada){
        try {
            String[] informacion_imagen = {MediaStore.Images.Media.DATA}; // Obtener la metadata de todas las imagenes guardadas en el dispositivo
            Cursor cursor = getContentResolver().query(imagenSeleccionada, informacion_imagen, null, null, null); // Buscar la imagen que coincide con el Uri dado
            int column_index = cursor.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);  // Buscar la columna de url de imagen
            cursor.moveToFirst(); // Ir al primer elemento
            return cursor.getString(column_index); // Regresar ruta real
        } catch (Exception e) {
            return imagenSeleccionada.getPath(); // Regresar ruta decodificada
        }
    }

    /**
     * Metodo que inicia la vista 'VistaPrevia'
     */
    public void iniciarVistaPrevia() {
        Intent intentVistaPrevia = new Intent(ActividadPrincipal.this, ActividadVistaPrevia.class); // Crear llamada para cambio de vista a 'VistaPrevia'
        intentVistaPrevia.putExtra("ruta_imagen", this.rutaImagen); // Agregar ruta de imagen a la llamada
        intentVistaPrevia.putExtra("latitud", this.latitud);
        intentVistaPrevia.putExtra("longitud", this.longitud);
        startActivity(intentVistaPrevia); // Iniciar el cambio de actividad
    }

    /**
     * Metodo que inicia la vista 'IniciarFormulario'
     */
    public void iniciarFormulario() {
        Intent intentFormulario = new Intent(ActividadPrincipal.this, ActividadFormulario.class); // Crear llamada para cambio de vista a 'Formulario'
        intentFormulario.putExtra("ruta_imagen", this.rutaImagen); // Agregar ruta de la imagen a la llamada
        startActivity(intentFormulario); // Empezar actividad
    }

    /**
     * Metodo que crea un archivo de imagen vacio donde se va a guardar la foto capturada
     * @return archivo creado
     */
    protected File crearArchivoSalida(){
        // To be safe, you should check that the SDCard is mounted
        // using Environment.getExternalStorageState() before doing this.

        File mediaStorageDir = new File(Environment.getExternalStoragePublicDirectory(
                Environment.DIRECTORY_PICTURES), "Watch_Animal");

        // Create the storage directory if it does not exist
        if (!mediaStorageDir.exists()){
            if (!mediaStorageDir.mkdirs()){
                //Log.d("MyCameraApp", "failed to create directory");
                return null;
            }
        }

        // Create a media file name
        String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss").format(new Date());
        File mediaFile;

        mediaFile = new File(mediaStorageDir.getPath() + File.separator +
                    "IMG_"+ timeStamp + ".jpg");
        return mediaFile;
    }

    /**
     * Metodo que recibe el resultado de la accion de tomar foto de la camara o escoger foto de la galeria
     *
     * @param requestCode codigo de la accion
     * @param resultCode codigo con resultado de la accion, para saber si fue exitosa
     * @param data informacion que regresa la accion
     */
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        try {

            if (requestCode == GALLERY_REQUEST && resultCode == RESULT_OK && data != null) { // Caso en el que la imagen se escoge de la galeria

                Uri imagenSeleccionada = data.getData(); // Obtener la información de la imagen
                this.rutaImagen = obtenerRutaRealUri(imagenSeleccionada); // Obtener ruta real de la imagen

                if (tieneCoordenadasImagen(rutaImagen))
                    iniciarVistaPrevia(); // Si la imagen tiene coordenadas ir a 'VistaPrevia'
                else
                    iniciarFormulario(); // En caso de que no existan coordenadas ir a vista 'Formulario'

            } else if(requestCode == CAM_REQUEST && resultCode == RESULT_OK) { // Caso en que la imagen se tomo de la camara

                Uri fotoCapturada = uriImagen; // Obtener informacion de la imagen
                this.rutaImagen = obtenerRutaRealUri(fotoCapturada); // Obtener ruta real de la imagen

                if (this.longitud != 0.0 && this.latitud != 0.0)
                    iniciarVistaPrevia(); // Si se pudieron obtener las coordenadas ir a 'VistaPrevia'
                else
                    iniciarFormulario(); // En caso de que no se puedan obtener las coordeandas ir a vista 'Formulario

            }else {

                Toast.makeText(this, "Favor de escoger una imagen",
                        Toast.LENGTH_LONG).show(); // Mostrar mensaje de error
                //Log.d("ActividadPrincipal", "resultCode= " + resultCode );
            }

        } catch (Exception e) {
            Toast.makeText(this, "Error en la aplicación, favor de intentar de nuevo", Toast.LENGTH_LONG)
                    .show(); // Mostrar mensaje de error
            //Log.d("ActividadPrincipal", "Error: " + e.toString());
        }

    }
}
