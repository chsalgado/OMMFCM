package com.example.george.ommfcm;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Matrix;
import android.media.ExifInterface;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Toast;

import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.PolylineOptions;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

public class ActividadMapa extends AppCompatActivity {
    private GoogleMap gMap; // Variable para invocar el mapa
    protected LatLng selected_location;
    protected String rutaImagen;
    protected ProgressDialog prDialog;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_mapa);

        Intent intent = getIntent(); // Variable para recuperar datos enviados por otra vista
        rutaImagen = intent.getStringExtra("ruta_imagen");

        gMap = ((MapFragment)getFragmentManager().findFragmentById(R.id.map)).getMap(); // Creacion del mapa
        prDialog = new ProgressDialog(ActividadMapa.this);
        prDialog.setCancelable(false);

        // Obtencion de variables de la ventana anterior
        final String origenEstado = getIntent().getStringExtra("OrigenEstado");
        final String origenMunicipio = getIntent().getStringExtra("OrigenMunicipio");
        final String destinoEstado = getIntent().getStringExtra("DestinoEstado");
        final String destinoMunicipio = getIntent().getStringExtra("DestinoMunicipio");

        new RutaMapa(this, gMap, origenEstado, origenMunicipio, destinoEstado, destinoMunicipio).execute(); // Calcular y dibjar ruta en mapa de acuerdo al origen y destino
    }

    @Override
    protected void onDestroy(){
        super.onDestroy();

        if(prDialog != null){
            prDialog.dismiss(); // Desechar dialogo de progreso cuando la aplicacion se cierre
        }
    }
    /**
     * TODO: Metodo que regresa a la vista anterior y pasa las coordenadas seleccionadas en el mapa
     * @param view
     */
    public void irActividadVistaPrevia(View view){
        if(selected_location != null) {
            Intent intent = new Intent(this, ActividadVistaPrevia.class);
            intent.putExtra("latitud", selected_location.latitude);
            intent.putExtra("longitud", selected_location.longitude);
            intent.putExtra("ruta_imagen", rutaImagen);

            this.startActivity(intent);
        }else{
            mostrarMensaje("Localización de incidente faltante", "Favor de seleccionar la ubicación aproximada donde ocurrió el incidente");
        }
    }


    private void mostrarMensaje(String titulo, String mensaje){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle(titulo);
        builder.setMessage(mensaje);
        builder.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                return;
            }
        });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    /**
     * Created by George on 10/7/15.
     *
     * Actividad asincrona donde se calcula una ruta en el mapa de acuerdo a un origen y un destino
     */
    private class RutaMapa extends AsyncTask<Void, Integer, Boolean> {
        private Context context; // Variable que guarda el contexto de la actividad donde se ejecutan la sfunciones
        private GoogleMap gMap; // Variable para acceso al mapa
        private String origenEstado; // Nombre del estado origen
        private String origenMunicipio; // Nombre del municipio origen
        private String destinoEstado; // Nombre del estado destino
        private String destinoMunicipio; // NOmbre del municipio destino
        private final ArrayList<LatLng> lstLatLng = new ArrayList<LatLng>(); // Arreglo de coordenadas para guardar la ruta
        private Marker user_marker; // Variable de marcador de mapa


        /**
         * Constructor de la clase
         * @param context contexto de la actividad donde se ejecuta la accion
         * @param gMap variable de accesso al mapa
         * @param origenEstado nombre del estado origen
         * @param origenMunicipio nombre del municipio origen
         * @param destinoEstado nombre del estado destino
         * @param destinoMunicipio nombre del municipio destino
         */
        public RutaMapa(final Context context, final GoogleMap gMap, final String origenEstado, final String origenMunicipio, final String destinoEstado, final String destinoMunicipio) {
            this.context = context;
            this.gMap= gMap;
            this.origenEstado = origenEstado;
            this.origenMunicipio = origenMunicipio;
            this.destinoEstado = destinoEstado;
            this.destinoMunicipio = destinoMunicipio;
        }

        @Override protected void onPreExecute() {
            prDialog.setMessage("Calculando ruta"); // Asignar mensaje al dialogo de progreso
            prDialog.show(); // Mostrar dialogo de progreso
        }

        @Override protected Boolean doInBackground(Void... params) {
            try {
                // Armar ruta para llamar al servicio de google con origen y destino
                final StringBuilder url = new StringBuilder("https://maps.googleapis.com/maps/api/directions/xml?");
                url.append("origin=");
                url.append(URLEncoder.encode(origenMunicipio.replace(' ', '+'), "utf-8"));
                url.append(",");
                url.append(URLEncoder.encode(origenEstado.replace(' ', '+'), "utf-8"));
                url.append("&destination=");
                url.append(URLEncoder.encode(destinoMunicipio.replace(' ', '+'), "utf-8"));
                url.append(",");
                url.append(URLEncoder.encode(destinoEstado.replace(' ', '+'), "utf-8"));
                url.append("&sensor=false&units=metric&mode=driving");

                // Realizar la conexion al servicio de google
                URL obj = new URL(url.toString());
                HttpURLConnection con = (HttpURLConnection) obj.openConnection();
                con.setRequestMethod("GET");
                con.connect();

                // Crear un documento para leer la respuesta del servidor
                final InputStream stream = con.getInputStream();
                final DocumentBuilderFactory documentBuilderFactory = DocumentBuilderFactory.newInstance();
                documentBuilderFactory.setIgnoringComments(true);
                final DocumentBuilder documentBuilder = documentBuilderFactory.newDocumentBuilder();
                final Document document = documentBuilder.parse(stream);
                document.getDocumentElement().normalize();
                final String status = document.getElementsByTagName("status").item(0).getTextContent();

                // En caso de que no haya respuesta de google regresar falso
                if(!"OK".equals(status)) {
                    return false;
                }

                // Guardar nodos de la ruta en una lista
                final Element elementLeg = (Element) document.getElementsByTagName("leg").item(0);
                final NodeList nodeListStep = elementLeg.getElementsByTagName("step");
                final int length = nodeListStep.getLength();

                // Decodificar nodos y dibujarlos en el mapa
                for(int i=0; i<length; i++) {
                    final Node nodeStep = nodeListStep.item(i);
                    if(nodeStep.getNodeType() == Node.ELEMENT_NODE) {
                        final Element elementStep = (Element) nodeStep;
                        decodePolylines(elementStep.getElementsByTagName("points").item(0).getTextContent());
                    }
                }
                return true;
            } catch(Exception e) {
                e.printStackTrace(); // Imprimir error
                prDialog.hide();
                mostrarMensaje("Ruta no encontrada", "Favor de especificar un nuevo origen y destino o regrese a la ventana anterior para volver a calcular la ruta");
                return false;
            }
        }

        /**
         * Metodo de decodifica un punto dado del servicio de google
         *
         * @param encodedPoints string con punto codificado
         */
        private void decodePolylines(final String encodedPoints) {
            int index = 0;
            int lat = 0, lng = 0;
            while (index < encodedPoints.length()) {
                int b, shift = 0, result = 0;
                do {
                    b = encodedPoints.charAt(index++) - 63;
                    result |= (b & 0x1f) << shift;
                    shift += 5;
                } while (b >= 0x20);
                int dlat = ((result & 1) != 0 ? ~(result >> 1) : (result >> 1));
                lat += dlat; shift = 0; result = 0; do { b = encodedPoints.charAt(index++) - 63;
                    result |= (b & 0x1f) << shift;
                    shift += 5;
                } while (b >= 0x20);
                int dlng = ((result & 1) != 0 ? ~(result >> 1) : (result >> 1));
                lng += dlng;
                lstLatLng.add(new LatLng((double)lat/1E5, (double)lng/1E5));
            }
        }

        @Override protected void onPostExecute(final Boolean result) {
            if(!result) {
                prDialog.hide();
                mostrarMensaje("Ruta no encontrada", "Favor de especificar un nuevo origen y destino o regrese a la ventana anterior para volver a calcular la ruta");
            } else {
                try {
                    final PolylineOptions polylines = new PolylineOptions(); // Crear arreglo de lineas
                    polylines.color(Color.BLUE); // Asignar un color a la linea

                    for(final LatLng latLng : lstLatLng) {
                        polylines.add(latLng); // Agregar lineas al arreglo
                    }

                    // Crear marcadores de inicio y final para la ruta
                    final MarkerOptions markerA = new MarkerOptions();
                    markerA.position(lstLatLng.get(0));
                    markerA.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_GREEN));
                    final MarkerOptions markerB = new MarkerOptions();
                    markerB.position(lstLatLng.get(lstLatLng.size() - 1));
                    markerB.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_RED));

                    LatLng first_coord = lstLatLng.get(0);
                    LatLng last_coord = lstLatLng.get(lstLatLng.size() - 1);

                    // Buscar coordenadas mas al suroeste y noreste
                    final LatLng southwest = getSouthwestCoord(first_coord, last_coord);
                    LatLng northeast = getNortheastCoord(first_coord, last_coord);
                    LatLngBounds bounds = new LatLngBounds(southwest, northeast);

                    // Agregar lineas y marcadores al mapa
                    gMap.moveCamera(CameraUpdateFactory.newLatLngBounds(bounds, 0));
                    gMap.addMarker(markerA);
                    gMap.addPolyline(polylines);
                    gMap.addMarker(markerB);

                    // Agregar listener al mapa para agregar un marcador en el lugar de la pantalla que toco el usuario
                    gMap.setOnMapClickListener(new GoogleMap.OnMapClickListener() {

                        /**
                         * Metodo que se llama al hacer click en algun lugar del mapa
                         *
                         * @param point coordenadas del lugar donde se dio click
                         */
                        @Override
                        public void onMapClick(LatLng point) {

                            // Si un marcador ya existe eliminarlo y crear uno nuevo en la nueva posicion
                            if(user_marker != null) {
                                user_marker.remove();
                                user_marker = null;
                            }
                            user_marker = gMap.addMarker(new MarkerOptions()
                                    .position(point)
                                    .draggable(true)
                                    .icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_AZURE)));
                            selected_location = point;
                        }
                    });
                    Toast.makeText(context, "Seleccione un punto en el mapa", Toast.LENGTH_LONG).show(); // Mostrar mensaje de carga
                    prDialog.hide();
                } catch (Exception e) {
                    e.printStackTrace(); // Imprimir error
                    prDialog.hide();
                    mostrarMensaje("Error al procesar la ruta", "Favor de regresar a la ventana anterior para volver a calcular la ruta");
                }
            }
        }

        /**
         * Metodo que regresa la coordenada mas al suroeste dadas 2 coordenadas
         *
         * @param coord1 coordenada a comparar
         * @param coord2 coordenada a comparar
         * @return coordenada mas al sureste de acuerdo a los dos puntos dados
         */
        private LatLng getSouthwestCoord(LatLng coord1, LatLng coord2){
            double westLat;
            double westLong;

            if(coord1.latitude < coord2.latitude)
                westLat = coord1.latitude;
            else
                westLat = coord2.latitude;


            if(coord1.longitude < coord2.longitude)
                westLong = coord1.longitude;
            else
                westLong = coord2.longitude;


            return new LatLng(westLat, westLong);
        }

        /**
         * Metodo que regresa la coordenada mas al noreste dadas 2 coordenadas
         *
         * @param coord1 coordenada a comparar
         * @param coord2 coordenada a comparar
         * @return coordenada mas al noroeste de acuerdo a los puntos dados
         */
        private LatLng getNortheastCoord(LatLng coord1, LatLng coord2){
            double eastLat;
            double eastLong;

            if(coord1.latitude > coord2.latitude)
                eastLat = coord1.latitude;
            else
                eastLat = coord2.latitude;

            if(coord1.longitude > coord2.longitude)
                eastLong = coord1.longitude;
            else
                eastLong = coord2.longitude;

            return new LatLng(eastLat, eastLong);
        }

    }

}
