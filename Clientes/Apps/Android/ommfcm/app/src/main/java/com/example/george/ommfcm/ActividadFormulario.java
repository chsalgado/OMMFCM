package com.example.george.ommfcm;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.location.LocationManager;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.location.Location;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.View;
import android.widget.*;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.common.api.GoogleApiClient.ConnectionCallbacks;
import com.google.android.gms.common.api.GoogleApiClient.OnConnectionFailedListener;
import com.google.android.gms.location.LocationServices;

public class ActividadFormulario extends AppCompatActivity implements
        ConnectionCallbacks, OnConnectionFailedListener {
    private static final int RESULT_LOAD = 1;
    private AlertDialog alert = null;
    protected TextView coordenadas;


    /**
     * Provides the entry point to Google Play services.
     */
    protected GoogleApiClient mGoogleApiClient;
    /**
     * Represents a geographical location.
     */
    protected Location mLastLocation;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_formulario);

        getGPS();
    }

    /**
     * Verifica si la ubicación está activa.
     */
    protected void getGPS() {
        //Administrador de la localización. Obtiene si el servicio de ubicación está activo.
        LocationManager locationManager;
        String context = Context.LOCATION_SERVICE;
        locationManager = (LocationManager)getSystemService(context);

        coordenadas = (TextView) findViewById(R.id.textTest);

        //Si la localización está desactivada, muestra una alerta para activarlo.
        if ( !locationManager.isProviderEnabled( LocationManager.GPS_PROVIDER ) ) {
            configuracionGPS();
        }
        //Una vez inicializado el servicio de ubicación, se inicia el servicio de Google
        buildGoogleApiClient();
    }

    /**
     * Muestra una alerta solicitando la activacion del servicio de ubicación
     */
    private void configuracionGPS() {
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

        //Crea y muestra la alerta creada.
        alert = builder.create();
        alert.show();
    }

    /**
     * Se construye el cliente que obtendrá la localización mediante los servicios de Google.
     * Se crea únicamente cuando la localización en el dispositivo está activa
     */
    protected synchronized void buildGoogleApiClient() {
        mGoogleApiClient = new GoogleApiClient.Builder(this)
                .addConnectionCallbacks((ConnectionCallbacks) this)
                .addOnConnectionFailedListener((OnConnectionFailedListener) this)
                .addApi(LocationServices.API)
                .build();
    }

    /**
     * Al iniciar el servicio, conecta el objeto mGoogleApiClient con los servicios de ubicación proporcionados por Google
     */
    @Override
    protected void onStart() {
        super.onStart();
        mGoogleApiClient.connect(); //Conecta con el servicio de ubicación de Google
    }

    /**
     * Al detenerse el servicio de ubicación, desconecta el objeto creado de los servicios de Google
     */
    @Override
    protected void onStop() {
        super.onStop();
        if (mGoogleApiClient.isConnected()) {
            mGoogleApiClient.disconnect(); //Detiene la conexión con el servicio de Google
        }
    }

    /**
     * Una vez que se conectó con los servicios de ubicación de google, obtiene las coordenadas de la posición actual
     * @param connectionHint
     */
    @Override
    public void onConnected(Bundle connectionHint) {
        //Obtiene la última localización conocida del dispositivo
        mLastLocation = LocationServices.FusedLocationApi.getLastLocation(mGoogleApiClient);
        if (mLastLocation != null) {
            coordenadas.setText(String.valueOf(mLastLocation.getLatitude()) + " - " + mLastLocation.getLongitude());
        } else {
            coordenadas.setText("No se detectaron coordenadas");
        }
    }

    /**
     * Mensaje de error en caso de que la conexión falle
     * @param result
     */
    @Override
    public void onConnectionFailed(ConnectionResult result) {
        coordenadas.setText("No se pudieron obtener las coordenadas");
    }

    /**
     * Mensaje de alerta en caso de que la conexión se haya suspendido
     * @param cause
     */
    @Override
    public void onConnectionSuspended(int cause) {
        coordenadas.setText("Conexión suspendida");
        mGoogleApiClient.connect();
    }

    /**
     * Al seleccionar el botón de cancelar, se regresa a la actividad principal
     * @param view
     */
    public void regresarPrincipal(View view) {
        Intent principalIntend = new Intent(this, ActividadPrincipal.class);
        startActivityForResult(principalIntend, RESULT_LOAD);
        finish();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_actividad_formulario, menu);
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
}
