package com.example.george.ommfcm;

import android.content.Context;
import android.os.AsyncTask;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import android.graphics.Color;
import android.view.View;
import android.widget.Toast;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.PolylineOptions;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

/**
 * Created by George on 10/7/15.
 */
public class RutaMapa extends AsyncTask<Void, Integer, Boolean> {
    private static final String TOAST_MSG = "Calculating"; private static final String TOAST_ERR_MAJ = "Impossible to trace Itinerary";
    private Context context;
    private GoogleMap gMap;
    private String origenEstado;
    private String origenMunicipio;
    private String destinoEstado;
    private String destinoMunicipio;
    private final ArrayList<LatLng> lstLatLng = new ArrayList<LatLng>();
    private Marker user_marker;

    public RutaMapa(final Context context, final GoogleMap gMap, final String origenEstado, final String origenMunicipio, final String destinoEstado, final String destinoMunicipio) {
        this.context = context;
        this.gMap= gMap;
        this.origenEstado = origenEstado;
        this.origenMunicipio = origenMunicipio;
        this.destinoEstado = destinoEstado;
        this.destinoMunicipio = destinoMunicipio;
    }

    @Override protected void onPreExecute() {
        Toast.makeText(context, TOAST_MSG, Toast.LENGTH_LONG).show();
    }

    @Override protected Boolean doInBackground(Void... params) {
        try {
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


            URL obj = new URL(url.toString());
            HttpURLConnection con = (HttpURLConnection) obj.openConnection();
            con.setRequestMethod("GET");
            con.connect();

            final InputStream stream = con.getInputStream();
            //String responseString = readStream(stream);
            final DocumentBuilderFactory documentBuilderFactory = DocumentBuilderFactory.newInstance();
            documentBuilderFactory.setIgnoringComments(true);
            final DocumentBuilder documentBuilder = documentBuilderFactory.newDocumentBuilder();
            final Document document = documentBuilder.parse(stream);
            document.getDocumentElement().normalize();
            final String status = document.getElementsByTagName("status").item(0).getTextContent();

            if(!"OK".equals(status)) {
                return false;
            }
            final Element elementLeg = (Element) document.getElementsByTagName("leg").item(0);
            final NodeList nodeListStep = elementLeg.getElementsByTagName("step");
            final int length = nodeListStep.getLength();
            for(int i=0; i<length; i++) {
                final Node nodeStep = nodeListStep.item(i);
                if(nodeStep.getNodeType() == Node.ELEMENT_NODE) {
                    final Element elementStep = (Element) nodeStep;
                    decodePolylines(elementStep.getElementsByTagName("points").item(0).getTextContent());
                }
            }
            return true;
        } catch(final Exception e) {
            e.printStackTrace();
            return false;
        }
    }

    private String readStream(InputStream in) {
        BufferedReader reader = null;
        StringBuffer response = new StringBuffer();
        try {
            reader = new BufferedReader(new InputStreamReader(in));
            String line = "";
            while ((line = reader.readLine()) != null) {
                response.append(line);
            }
        } catch (IOException e) {
            e.printStackTrace();
        } finally {
            if (reader != null) {
                try {
                    reader.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }
        return response.toString();
    }

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
            Toast.makeText(context, TOAST_ERR_MAJ, Toast.LENGTH_SHORT).show();
        } else {
            try {
                final PolylineOptions polylines = new PolylineOptions();
                polylines.color(Color.BLUE);
                for(final LatLng latLng : lstLatLng) {
                    polylines.add(latLng);
                }
                final MarkerOptions markerA = new MarkerOptions();
                markerA.position(lstLatLng.get(0));
                markerA.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_GREEN));
                final MarkerOptions markerB = new MarkerOptions();
                markerB.position(lstLatLng.get(lstLatLng.size() - 1));
                markerB.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_RED));

                LatLng first_coord = lstLatLng.get(0);
                LatLng last_coorf = lstLatLng.get(lstLatLng.size() - 1);

                LatLng southwest = getSouthwestCoord(lstLatLng.get(0), lstLatLng.get(lstLatLng.size() - 1));
                LatLng northeast = getNortheastCoord(lstLatLng.get(0), lstLatLng.get(lstLatLng.size() - 1));
                LatLngBounds bounds = new LatLngBounds(southwest, northeast);

                gMap.moveCamera(CameraUpdateFactory.newLatLngBounds(bounds, 0));
                gMap.addMarker(markerA);
                gMap.addPolyline(polylines);
                gMap.addMarker(markerB);

                gMap.setOnMapClickListener(new GoogleMap.OnMapClickListener() {

                    @Override
                    public void onMapClick(LatLng point) {

                        if(user_marker != null) {
                            user_marker.remove();
                            user_marker = null;
                        }
                        user_marker = gMap.addMarker(new MarkerOptions()
                                .position(point)
                                .draggable(true));
                    }
                });

            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

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
