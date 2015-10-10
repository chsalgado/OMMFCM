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
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.JsonHttpResponseHandler;

import org.apache.http.Header;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class ActividadFormulario extends AppCompatActivity {

    private static final int MAPA = 1000;
    private String rutaImagen;
    private List<Estado> listaEstados;
    private List<String> listaNombresEstados;
    private List<Municipio> listaMunicipiosOrigen;
    private List<String> listaNombresMunicipiosOrigen;
    private List<Municipio> listaMunicipiosDestino;
    private List<String> listaNombresMunicipiosDestino;
    private static final String rutaEstados = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/estados";
    private static final String rutaMunicipios = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/municipios?estado=";
    /**
     * Metodo que se llama la primera vez que se crea la vista
     * @param savedInstanceState
     */
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_formulario);

        Intent intent = getIntent();

        rutaImagen = intent.getStringExtra("ruta_imagen");

        conseguirEstados();
    }

    private void conseguirEstados(){
        this.listaEstados = new ArrayList<Estado>();
        this.listaNombresEstados = new ArrayList<String>();
        final Context ctx = this.getApplicationContext();
        AsyncHttpClient cliente = new AsyncHttpClient();
        cliente.get(this.rutaEstados, new JsonHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                try {
                    JSONArray jarr = responseBody.getJSONArray("estados");
                    for (int i = 0; i < jarr.length(); i++) {
                        JSONObject r = jarr.getJSONObject(i);
                        listaEstados.add(new Estado(r.getInt("id_estado"), r.getString("estado")));
                        listaNombresEstados.add(r.getString("estado"));
                    }

                    AutoCompleteTextView txt_origen_estados = (AutoCompleteTextView) findViewById(R.id.txt_origen_estado);
                    ArrayAdapter<String> adaptadorEstados = new ArrayAdapter<String>(ctx,
                            android.R.layout.simple_dropdown_item_1line, listaNombresEstados);
                    txt_origen_estados.setAdapter(adaptadorEstados);

                    AutoCompleteTextView txt_destino_estados = (AutoCompleteTextView) findViewById(R.id.txt_destino_estado);
                    txt_destino_estados.setAdapter(adaptadorEstados);

                    addListenerOrigenEstado(txt_origen_estados);
                    addListenerDestinoEstado(txt_destino_estados);

                } catch (Exception e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                error.printStackTrace();
            }
        });
    }

    public void conseguirMunicipiosOrigen(int idEstado){
        this.listaMunicipiosOrigen = new ArrayList<Municipio>();
        this.listaNombresMunicipiosOrigen = new ArrayList<String>();
        final Context ctx = this.getApplicationContext();
        AsyncHttpClient cliente = new AsyncHttpClient();
        cliente.get(this.rutaMunicipios + idEstado, new JsonHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                try {
                    JSONArray jarr = responseBody.getJSONArray("municipios");
                    for (int i = 0; i < jarr.length(); i++) {
                        JSONObject r = jarr.getJSONObject(i);
                        listaMunicipiosOrigen.add(new Municipio(r.getInt("id_municipio"), r.getString("nombre_municipio")));
                        listaNombresMunicipiosOrigen.add(r.getString("nombre_municipio"));
                    }

                    AutoCompleteTextView txt_origen_municipio = (AutoCompleteTextView) findViewById(R.id.txt_origen_municipio);
                    ArrayAdapter<String> adaptadorMunicipios = new ArrayAdapter<String>(ctx,
                            android.R.layout.simple_dropdown_item_1line, listaNombresMunicipiosOrigen);
                    txt_origen_municipio.setAdapter(adaptadorMunicipios);

                } catch (Exception e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                error.printStackTrace();
            }
        });
    }

    public void conseguirMunicipiosDestino(int idEstado){
        this.listaMunicipiosDestino = new ArrayList<Municipio>();
        this.listaNombresMunicipiosDestino = new ArrayList<String>();
        final Context ctx = this.getApplicationContext();
        AsyncHttpClient cliente = new AsyncHttpClient();
        cliente.get(this.rutaMunicipios + idEstado, new JsonHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                try {
                    JSONArray jarr = responseBody.getJSONArray("municipios");
                    for (int i = 0; i < jarr.length(); i++) {
                        JSONObject r = jarr.getJSONObject(i);
                        listaMunicipiosDestino.add(new Municipio(r.getInt("id_municipio"), r.getString("nombre_municipio")));
                        listaNombresMunicipiosDestino.add(r.getString("nombre_municipio"));
                    }

                    AutoCompleteTextView txt_destino_municipio = (AutoCompleteTextView) findViewById(R.id.txt_destino_municipio);
                    ArrayAdapter<String> adaptadorMunicipios = new ArrayAdapter<String>(ctx,
                            android.R.layout.simple_dropdown_item_1line, listaNombresMunicipiosDestino);
                    txt_destino_municipio.setAdapter(adaptadorMunicipios);

                } catch (Exception e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                error.printStackTrace();
            }
        });
    }

    public void addListenerOrigenEstado(AutoCompleteTextView txt_origen_estado){
        txt_origen_estado.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                String seleccion = (String) parent.getItemAtPosition(position);

                for (int i = 0; i < listaEstados.size(); i++) {
                    if (listaEstados.get(i).getNombre().equals(seleccion)) {
                        conseguirMunicipiosOrigen(listaEstados.get(i).getId());
                    }
                }
            }
        });
    }

    public void addListenerDestinoEstado(AutoCompleteTextView txt_destino_estado){
        txt_destino_estado.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                String seleccion = (String) parent.getItemAtPosition(position);

                for(int i = 0; i < listaEstados.size(); i++){
                    if(listaEstados.get(i).getNombre().equals(seleccion)){
                        conseguirMunicipiosDestino(listaEstados.get(i).getId());
                    }
                }
            }
        });
    }

    public void mostrarMapa(View view){

        AutoCompleteTextView txt_origen_estado = (AutoCompleteTextView) findViewById(R.id.txt_origen_estado);
        AutoCompleteTextView txt_origen_municipio = (AutoCompleteTextView) findViewById(R.id.txt_origen_municipio);
        AutoCompleteTextView txt_destino_estado = (AutoCompleteTextView) findViewById(R.id.txt_destino_estado);
        AutoCompleteTextView txt_destino_municipio = (AutoCompleteTextView) findViewById(R.id.txt_destino_municipio);

        final Intent intent = new Intent(this, ActividadMapa.class);
        intent.putExtra("OrigenEstado", txt_origen_estado.getText().toString().trim());
        intent.putExtra("OrigenMunicipio", txt_origen_municipio.getText().toString().trim());
        intent.putExtra("DestinoEstado", txt_destino_estado.getText().toString().trim());
        intent.putExtra("DestinoMunicipio", txt_destino_municipio.getText().toString().trim());

        this.startActivityForResult(intent, MAPA);
    }


    public void irVistaPrevia(View view){
        Intent intent = new Intent(this, ActividadVistaPrevia.class);
        intent.putExtra("ruta_imagen", rutaImagen);
        this.startActivity(intent);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (requestCode == MAPA && resultCode == RESULT_OK && data != null) {
            
        }
    }

}
