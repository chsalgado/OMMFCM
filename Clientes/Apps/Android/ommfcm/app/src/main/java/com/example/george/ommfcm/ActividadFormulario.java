package com.example.george.ommfcm;

import android.app.AlertDialog;
import android.app.ProgressDialog;
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
import android.view.MotionEvent;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
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
    private List<Municipio> listaMunicipiosOrigen; // Lista para los municipios origen guardados en el servidor con id
    private List<Municipio> listaMunicipiosDestino; // Lista para los municipios destino guardados en el servidor con id
    private static final String rutaEstados = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/estados"; // Variable que contiene la ruta del servidor para recuperar los estados
    private static final String rutaMunicipios = "http://148.243.51.170:8007/obsfauna/public_html/index.php/api/municipios?estado="; // Variable que contiene la ruta del servidor para recuperar los municipios
    private ProgressBar pb_origen_estado; // icono de progreso que muestra la descarga de estados del servidor
    private ProgressBar pb_origen_municipio; // icono de progreso que muestra la descarga de estados del servidor
    private ProgressBar pb_destino_estado; // icono de progreso que muestra la descarga de municipios del servidor
    private ProgressBar pb_destino_municipio; // icono de progreso que muestra la descarga de municipios del servidor
    private AutoCompleteTextView actv_origen_estado; // variable de referencia al campo de texto con autompletar de estados origen
    private AutoCompleteTextView actv_origen_municipio; // variable de referencia al campo de texto con autompletar de estados destino
    private AutoCompleteTextView actv_destino_estado; // variable de referencia al campo de texto con autompletar de municipios origen
    private AutoCompleteTextView actv_destino_municipio; // variable de referencia al campo de texto con autompletar de municipios destino
    private Estado estado_origen_seleccionado;
    private Estado estado_destino_seleccionado;
    private Municipio municipio_origen_seleccionado;
    private Municipio municipio_destino_seleccionado;

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

        // Referenciar variables con elementos de la vista
        pb_origen_estado = (ProgressBar) findViewById(R.id.pb_origen_estado);
        pb_destino_estado = (ProgressBar) findViewById(R.id.pb_destino_estado);
        pb_origen_municipio = (ProgressBar) findViewById(R.id.pb_origen_municipio);
        pb_destino_municipio = (ProgressBar) findViewById(R.id.pb_destino_municipio);

        actv_origen_estado = (AutoCompleteTextView) findViewById(R.id.txt_origen_estado);
        actv_destino_estado = (AutoCompleteTextView) findViewById(R.id.txt_destino_estado);
        actv_origen_municipio = (AutoCompleteTextView) findViewById(R.id.txt_origen_municipio);
        actv_destino_municipio = (AutoCompleteTextView) findViewById(R.id.txt_destino_municipio);

        // Establecer todos los campos de texto como inhabilitados
        actv_origen_estado.setEnabled(false);
        actv_destino_estado.setEnabled(false);
        actv_origen_municipio.setEnabled(false);
        actv_destino_municipio.setEnabled(false);

        conseguirEstados(); // Llamada a metodo para recuperar los estados del servidor
    }

    /**
     * Metodo que recupera los estados del servidor y los agrega al elemento de autocompletar
     */
    private void conseguirEstados(){
        // Inicizalizacion de variables
        this.listaEstados = new ArrayList<Estado>();
        final Context ctx = this.getApplicationContext();
        AsyncHttpClient cliente = new AsyncHttpClient();

        pb_origen_estado.setVisibility(View.VISIBLE); // Mostrar spinner carga estados origen
        pb_destino_estado.setVisibility(View.VISIBLE); // Mostrar spinner cargar estados destino

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
                    }

                    AdaptadorEstado adaptadorEstados = new AdaptadorEstado(ctx, R.layout.activity_actividad_formulario, R.id.nombre_estado, listaEstados); // Creacion de un adaptador para el elemento de autocompletar con la lista de nombres de estados
                    actv_origen_estado.setAdapter(adaptadorEstados); // Agregar adaptador al elemento de autocompletar para estados origen
                    actv_destino_estado.setAdapter(adaptadorEstados); // Agregar adaptador al elemento de autocompletar para estados destino

                    addListenerOrigenEstado(actv_origen_estado); // Agregar un listener al autocompletar de estados origen
                    addListenerDestinoEstado(actv_destino_estado); // Agregar un listener al autocompletar de estados destino
                    addListenerOrigenMunicipio(actv_origen_municipio);
                    addListenerDestinoMunicipio(actv_destino_municipio);

                    actv_origen_estado.setEnabled(true);
                    actv_destino_estado.setEnabled(true);

                } catch (Exception e) {
                    e.printStackTrace(); // Imprimir la pila del error
                }

                pb_origen_estado.setVisibility(View.INVISIBLE); // Ocultar spinner carga estados origen
                pb_destino_estado.setVisibility(View.INVISIBLE); // Ocultar spinner carga estados destino
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
                pb_origen_estado.setVisibility(View.INVISIBLE); // Ocultar spinner carga estados origen
                pb_destino_estado.setVisibility(View.INVISIBLE); // Ocultar spinner carga estados destino
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
                    }

                    AutoCompleteTextView txt_origen_municipio = (AutoCompleteTextView) findViewById(R.id.txt_origen_municipio); // Asignar variable a elemento de autocompletar

                    AdaptadorMunicipio adaptadorMunicipios = new AdaptadorMunicipio(ctx,
                            R.layout.activity_actividad_formulario, R.id.nombre_estado, listaMunicipiosOrigen); // Creacion de adaptador para el autocompletar con el arrreglo de elementos a buscar
                    txt_origen_municipio.setAdapter(adaptadorMunicipios); // Agregar adapatdor al elemento de autocompletar
                    actv_origen_municipio.setEnabled(true);

                } catch (Exception e) {

                    e.printStackTrace(); // Imprimir pila de error
                }
                pb_origen_municipio.setVisibility(View.INVISIBLE);

            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                pb_origen_municipio.setVisibility(View.INVISIBLE);
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
                    }

                    AutoCompleteTextView txt_destino_municipio = (AutoCompleteTextView) findViewById(R.id.txt_destino_municipio); // Asignar variable a elemento de autocompletar
                    AdaptadorMunicipio adaptadorMunicipios = new AdaptadorMunicipio(ctx,
                            R.layout.activity_actividad_formulario, R.id.nombre_estado, listaMunicipiosDestino); // Creacion de adaptador para el autocompletar con el arrreglo de elementos a buscar
                    txt_destino_municipio.setAdapter(adaptadorMunicipios); // Agregar adaptador al elemento de atucompletar
                    actv_destino_municipio.setEnabled(true);
                } catch (Exception e) {
                    e.printStackTrace(); // Imprimir  pila de errores
                }
                pb_destino_municipio.setVisibility(View.INVISIBLE);
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, String res, Throwable
                    error) {
                pb_destino_municipio.setVisibility(View.INVISIBLE);
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
                Estado seleccion = (Estado) parent.getItemAtPosition(position); // Obtiene el string de la opcion seleccionada en el autocompletar

                estado_origen_seleccionado = seleccion;
                actv_destino_municipio.setEnabled(false);
                pb_origen_municipio.setVisibility(View.VISIBLE);
                conseguirMunicipiosOrigen(seleccion.getId()); // Si se encuentra el elemento se buscan los municipios asociados al estado
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
                Estado seleccion = (Estado) parent.getItemAtPosition(position); // Obtiene el string de la opcion seleccionada en el autocompletar

                estado_destino_seleccionado = seleccion;
                actv_destino_municipio.setEnabled(false);
                pb_destino_municipio.setVisibility(View.VISIBLE);
                conseguirMunicipiosDestino(seleccion.getId()); // Si se encuentra el elemento se buscan los municipios asociados al estado
            }
        });
    }

    public void addListenerOrigenMunicipio(AutoCompleteTextView txt_origen_municipio){
        txt_origen_municipio.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                Municipio seleccion = (Municipio) parent.getItemAtPosition(position); // Obtiene el objeto de la opcion seleccionada en el autocompletar
                municipio_origen_seleccionado = seleccion;
            }
        });
    }

    public void addListenerDestinoMunicipio(AutoCompleteTextView txt_destino_municipio){
        txt_destino_municipio.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                Municipio seleccion = (Municipio) parent.getItemAtPosition(position); // Obtiene el objeto de la opcion seleccionada en el autocompletar
                municipio_destino_seleccionado = seleccion;
            }
        });
    }

    /**
     * Metodo que lleva a la actividad de VistaPrevia
     *
     * @param view
     */
    public void irActividadMapa(View view){

        if(validacionCamposTexto()) {
            AutoCompleteTextView txt_origen_estado = (AutoCompleteTextView) findViewById(R.id.txt_origen_estado);
            AutoCompleteTextView txt_origen_municipio = (AutoCompleteTextView) findViewById(R.id.txt_origen_municipio);
            AutoCompleteTextView txt_destino_estado = (AutoCompleteTextView) findViewById(R.id.txt_destino_estado);
            AutoCompleteTextView txt_destino_municipio = (AutoCompleteTextView) findViewById(R.id.txt_destino_municipio);

            final Intent intent = new Intent(this, ActividadMapa.class);
            intent.putExtra("OrigenEstado", txt_origen_estado.getText().toString().trim());
            intent.putExtra("OrigenMunicipio", txt_origen_municipio.getText().toString().trim());
            intent.putExtra("DestinoEstado", txt_destino_estado.getText().toString().trim());
            intent.putExtra("DestinoMunicipio", txt_destino_municipio.getText().toString().trim());
            intent.putExtra("idMunicipioOrigen", this.municipio_origen_seleccionado.getId());
            intent.putExtra("idMunicipioDestino", this.municipio_destino_seleccionado.getId());
            intent.putExtra("ruta_imagen", this.rutaImagen);

            this.startActivity(intent);
        }
    }

    /**
     * Metodo para validar los campos de texto para estado/municipio origen/destino
     * @return true si los campos contienen datos validos, false en caso contrario
     */
    public boolean validacionCamposTexto(){

        String origen_estado_txt = actv_origen_estado.getText().toString().trim();
        String origen_municipio_txt = actv_origen_municipio.getText().toString().trim();
        String destino_estado_txt = actv_destino_estado.getText().toString().trim();
        String destino_muicipio_txt = actv_destino_municipio.getText().toString().trim();

        if(origen_estado_txt.equals("") || origen_municipio_txt.equals("") || destino_estado_txt.equals("") || destino_muicipio_txt.equals("")){
            mostrarMensaje("Campos faltantes", "Favor de especificar un estado/municipio origen y destino");
            return false;
        }

        if(this.estado_origen_seleccionado == null){
            mostrarMensaje("Estado origen invalido", "Favor de seleccionar un estado valido");
            return false;
        }

        if(this.estado_destino_seleccionado == null){
            mostrarMensaje("Estado destino invalido", "Favor de seleccionar un estado valido");
            return false;
        }

        if(this.municipio_origen_seleccionado == null){
            mostrarMensaje("Municipio origen invalido", "Favor de seleccionar un municipio valido");
            return false;
        }

        if(this.municipio_destino_seleccionado == null){
            mostrarMensaje("Municipio destino invalido", "Favor de seleccionar un municipio valido");
            return false;
        }

        return true;
    }

    public void mostrarMensaje(String titulo, String mensaje){
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
     * Metodo que cierra el teclado cuando se toca la pantalla
     * @param event
     * @return
     */
    @Override
    public boolean dispatchTouchEvent(MotionEvent event) {
        View view = getCurrentFocus();
        boolean ret = super.dispatchTouchEvent(event);

        if (view instanceof EditText) {
            View w = getCurrentFocus();
            int scrcoords[] = new int[2];
            w.getLocationOnScreen(scrcoords);
            float x = event.getRawX() + w.getLeft() - scrcoords[0];
            float y = event.getRawY() + w.getTop() - scrcoords[1];

            if (event.getAction() == MotionEvent.ACTION_UP
                    && (x < w.getLeft() || x >= w.getRight()
                    || y < w.getTop() || y > w.getBottom()) ) {
                InputMethodManager imm = (InputMethodManager)getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(), 0);
            }
        }
        return ret;
    }
}
