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

    private static final int MAPA = 1000; // Codigo para recibir la finalizacion de actividad mapa
    private String rutaImagen; // Variable para guardar la ruta de imagen seleccionada de la vista pasada
    private List<Estado> listaEstados; // Lista para los estados guardados en el servidor con id
    private List<String> listaNombresEstados; // Lista para los estados guardados en el servidor sin id para la busqueda en el autocompletar
    private List<Municipio> listaMunicipiosOrigen; // Lista para los municipios origen guardados en el servidor con id
    private List<String> listaNombresMunicipiosOrigen; // Lista para los municipios origen guardados en el servidor sin id para la busqueda en el autocompletar
    private List<Municipio> listaMunicipiosDestino; // Lista para los municipios destino guardados en el servidor con id
    private List<String> listaNombresMunicipiosDestino; // Lista para los municipios destino guardados en el servidor sin id para la busqueda en el autocompletar
    private static final String rutaEstados = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/estados"; // Variable que contiene la ruta del servidor para recuperar los estados
    private static final String rutaMunicipios = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/municipios?estado="; // Variable que contiene la ruta del servidor para recuperar los municipios

    /**
     * Metodo que se llama la primera vez que se crea la vista
     * @param savedInstanceState
     */
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_actividad_formulario);

        Intent intent = getIntent(); // Variable para recuperar datos enviados por otra vista
        rutaImagen = intent.getStringExtra("ruta_imagen"); // Recuperacion de la variable de ruta de imagne de la vista pasada
        conseguirEstados(); // Llamada a metodo para recuperar los estados del servidor
    }

    /**
     * Metodo que recupera los estados del servidor y los agrega al elemento de autocompletar
     */
    private void conseguirEstados(){
        // Inicizalizacion de variables
        this.listaEstados = new ArrayList<Estado>();
        this.listaNombresEstados = new ArrayList<String>();
        final Context ctx = this.getApplicationContext();
        AsyncHttpClient cliente = new AsyncHttpClient();

        // Llamada GET al servidor
        cliente.get(this.rutaEstados, new JsonHttpResponseHandler() {

            /**
             * Metodo que se llama cuando la respuesta del servidor ha sido exitosa
             *
             * @param statusCode numero entero con el codigo de estado
             * @param headers Arreglo de encabezados de la respuesta
             * @param responseBody Objeto con la respuesta del servidor
             */
            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                try {
                    JSONArray jarr = responseBody.getJSONArray("estados"); // Arreglo que recupera los estados de la respuesta del servidor

                    // Ciclo for que agrega los elementos del JSON a las listas de la clase
                    for (int i = 0; i < jarr.length(); i++) {
                        JSONObject r = jarr.getJSONObject(i);
                        listaEstados.add(new Estado(r.getInt("id_estado"), r.getString("estado"))); // Agregar objeto de estado a la lista de estados
                        listaNombresEstados.add(r.getString("estado")); // Agregar nombre de estado a la lista de nombres de estados
                    }

                    AutoCompleteTextView txt_origen_estados = (AutoCompleteTextView) findViewById(R.id.txt_origen_estado); // Asignar elemento visual de autocompletar para estados origen a una variable para poder editarlo
                    ArrayAdapter<String> adaptadorEstados = new ArrayAdapter<String>(ctx,
                            android.R.layout.simple_dropdown_item_1line, listaNombresEstados); // Creacion de un adaptador para el elemento de autocompletar con la lista de nombres de estados
                    txt_origen_estados.setAdapter(adaptadorEstados); // Agregar adaptador al elemento de autocompletar para estados origen

                    AutoCompleteTextView txt_destino_estados = (AutoCompleteTextView) findViewById(R.id.txt_destino_estado); // Asignar elemento visual de autocompletar para estados destino a una variable para poder editarlo
                    txt_destino_estados.setAdapter(adaptadorEstados); // Agregar adaptador al elemento de autocompletar para estados destino

                    addListenerOrigenEstado(txt_origen_estados); // Agregar un listener al autocompletar de estados origen
                    addListenerDestinoEstado(txt_destino_estados); // Agregar un listener al autocompletar de estados destino

                } catch (Exception e) {
                    e.printStackTrace(); // Imprimir la pila del error
                }
            }

            /**
             * Metodo que se llama en caso de falla al realizar la llamada al servidor
             *
             * @param statusCode variable entera que indica el codigo de error
             * @param headers arreglo con los encabezados de la respuesta de error
             * @param res variable con la respuesta del servidor como cadena de caracteres
             * @param error variable con el error java
             */
            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                error.printStackTrace(); // Imprimir la pila del error
            }
        });
    }

    /**
     * Metodo que recupera los municipios del servidor a partir del id de un estado seleccionado y los asigna al elemento autocompletar
     *
     * @param idEstado numero entero con el id del estado seleccionado
     */
    public void conseguirMunicipiosOrigen(int idEstado){
        // Inicializacion de variables
        this.listaMunicipiosOrigen = new ArrayList<Municipio>();
        this.listaNombresMunicipiosOrigen = new ArrayList<String>();
        final Context ctx = this.getApplicationContext();
        AsyncHttpClient cliente = new AsyncHttpClient();

        // Llamada GET al servidor
        cliente.get(this.rutaMunicipios + idEstado, new JsonHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                try {
                    JSONArray jarr = responseBody.getJSONArray("municipios"); // Arreglo donde se recuperan los municipios enviados por el servidor

                    // Ciclo for que agrega los elementos del JSON a las listas de la clase principal
                    for (int i = 0; i < jarr.length(); i++) {
                        JSONObject r = jarr.getJSONObject(i);
                        listaMunicipiosOrigen.add(new Municipio(r.getInt("id_municipio"), r.getString("nombre_municipio"))); // Agregar objeto municipio al arreglo de municipios
                        listaNombresMunicipiosOrigen.add(r.getString("nombre_municipio")); // Agregar nombre de municipios a la lista de nombres
                    }

                    AutoCompleteTextView txt_origen_municipio = (AutoCompleteTextView) findViewById(R.id.txt_origen_municipio); // Asignar variable a elemento de autocompletar
                    ArrayAdapter<String> adaptadorMunicipios = new ArrayAdapter<String>(ctx,
                            android.R.layout.simple_dropdown_item_1line, listaNombresMunicipiosOrigen); // Creacion de adaptador para el autocompletar con el arrreglo de elementos a buscar
                    txt_origen_municipio.setAdapter(adaptadorMunicipios); // Agregar adapatdor al elemento de autocompletar

                } catch (Exception e) {
                    e.printStackTrace(); // Imprimir pila de error
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                error.printStackTrace(); // Imprimir pila de error
            }
        });
    }

    /**
     * Metodo que recupera los municipios del servidor a partir del id de un estado y lo asigna al elemento de autocompletar de municipios destino
     *
     * @param idEstado
     */
    public void conseguirMunicipiosDestino(int idEstado){
        // Inicializacion de variables
        this.listaMunicipiosDestino = new ArrayList<Municipio>();
        this.listaNombresMunicipiosDestino = new ArrayList<String>();
        final Context ctx = this.getApplicationContext();
        AsyncHttpClient cliente = new AsyncHttpClient();

        // Llamada GET al servidor
        cliente.get(this.rutaMunicipios + idEstado, new JsonHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, JSONObject responseBody) {
                try {
                    JSONArray jarr = responseBody.getJSONArray("municipios"); // Arreglo donde se recuperan los municipios enviados

                    // Ciclo que agrega los elementos del JSON a las listas de municipio destino
                    for (int i = 0; i < jarr.length(); i++) {
                        JSONObject r = jarr.getJSONObject(i);
                        listaMunicipiosDestino.add(new Municipio(r.getInt("id_municipio"), r.getString("nombre_municipio")));
                        listaNombresMunicipiosDestino.add(r.getString("nombre_municipio"));
                    }

                    AutoCompleteTextView txt_destino_municipio = (AutoCompleteTextView) findViewById(R.id.txt_destino_municipio); // Asignar variable a elemento de autocompletar
                    ArrayAdapter<String> adaptadorMunicipios = new ArrayAdapter<String>(ctx,
                            android.R.layout.simple_dropdown_item_1line, listaNombresMunicipiosDestino); // Creacion de adaptador para el autocompletar con el arreglo de elementos a buscar
                    txt_destino_municipio.setAdapter(adaptadorMunicipios); // Agregar adaptador al elemento de atucompletar

                } catch (Exception e) {
                    e.printStackTrace(); // Imprimir  pila de errores
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                error.printStackTrace(); // Imprimir pila de errores
            }
        });
    }

    /**
     * Metodo que agrega un listener al elemento de autocompletar de estado destino y busca la opcion
     * seleccionada en la lista de objetos estados para cargar los municipios correspondientes a ese estado
     *
     * @param txt_origen_estado elemento de autocompletar al que se le va a agregar el listener
     */
    public void addListenerOrigenEstado(AutoCompleteTextView txt_origen_estado){
        txt_origen_estado.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                String seleccion = (String) parent.getItemAtPosition(position); // Obtiene el string de la opcion seleccionada en el autocompletar

                // Ciclo que busca la seleccion en la lista de objetos estado
                for (int i = 0; i < listaEstados.size(); i++) {
                    if (listaEstados.get(i).getNombre().equals(seleccion)) {
                        conseguirMunicipiosOrigen(listaEstados.get(i).getId()); // Si se encuentra el elemento se buscan los municipios asociados al estado
                    }
                }
            }
        });
    }

    /**
     *  Metodo que agrega un listener al elemento de autocompletar de estado destino y busca la opcion
     *  seleccionada en la lista de objetos estados para cargar los municipios correspondientes a ese estado
     *
     * @param txt_destino_estado elemento de autocompletar al que se le va a agregar el listener
     */
    public void addListenerDestinoEstado(AutoCompleteTextView txt_destino_estado){
        txt_destino_estado.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                String seleccion = (String) parent.getItemAtPosition(position); // Obtiene el string de la opcion seleccionada en el autocompletar

                // Ciclo que busca la seleccion en la lista de objetos estado
                for(int i = 0; i < listaEstados.size(); i++){
                    if(listaEstados.get(i).getNombre().equals(seleccion)){
                        conseguirMunicipiosDestino(listaEstados.get(i).getId()); // Si se encuentra el elemento se buscan los municipios asociados al estado
                    }
                }
            }
        });
    }

    /**
     * Metodo que llama a la actividad para mostrar el mapa
     * @param view
     */
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

    /**
     * Metodo que lleva a la actividad de VistaPrevia
     *
     * @param view
     */
    public void irVistaPrevia(View view){
        Intent intent = new Intent(this, ActividadVistaPrevia.class);
        intent.putExtra("ruta_imagen", rutaImagen);
        this.startActivity(intent);
    }

    /**
     * TODO: Metodo que se llama al regresar de la ventana de mapa
     *
     * @param requestCode numero que identifica a la ventana de donde se esta regresando
     * @param resultCode numero con el codigo del resultado
     * @param data informacion que se regresa de la ventana
     */
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (requestCode == MAPA && resultCode == RESULT_OK && data != null) {

        }
    }

}
