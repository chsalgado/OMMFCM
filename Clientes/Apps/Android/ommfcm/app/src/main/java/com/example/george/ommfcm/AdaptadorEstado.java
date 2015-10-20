package com.example.george.ommfcm;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.TextView;

import com.google.android.gms.plus.People;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by George on 10/17/15.
 */
public class AdaptadorEstado extends ArrayAdapter<Estado> {
    Context context;
    int resource, textViewResourceId;
    List<Estado> listaEstados, listaTemporal, listaSugerencias;

    public AdaptadorEstado(Context context, int resource, int textViewResourceId, List<Estado> listaEstados){
        super(context, resource,textViewResourceId, listaEstados);
        this.context = context;
        this.resource = resource;
        this.textViewResourceId = textViewResourceId;
        this.listaEstados = listaEstados;
        listaTemporal = new ArrayList<Estado>(listaEstados);
        listaSugerencias = new ArrayList<Estado>();
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent){
        View view = convertView;

        if(convertView == null){
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            view = inflater.inflate(R.layout.fila_resultado_estado, parent, false);
        }

        Estado estado = listaEstados.get(position);
        if(estado != null){
            TextView nombre = (TextView) view.findViewById(R.id.nombre_estado);
            if(nombre != null){
                nombre.setText(estado.getNombre());
            }
        }

        return view;
    }

    @Override
    public Filter getFilter(){
        return filtroNombres;
    }

    Filter filtroNombres = new Filter() {
        @Override
        public CharSequence convertResultToString(Object resultValue) {
            String str = ((Estado) resultValue).getNombre();
            return str;
        }

        @Override
        protected FilterResults performFiltering(CharSequence constraint) {
            if (constraint != null) {
                listaSugerencias.clear();
                for (Estado estado : listaTemporal) {
                    if (estado.getNombre().toLowerCase().contains(constraint.toString().toLowerCase())) {
                        listaSugerencias.add(estado);
                    }
                }
                FilterResults resultados_filtro = new FilterResults();
                resultados_filtro.values = listaSugerencias;
                resultados_filtro.count = listaSugerencias.size();
                return resultados_filtro;
            } else {
                return new FilterResults();
            }
        }

        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            List<Estado> filterList = (ArrayList<Estado>) results.values;
            if (results != null && results.count > 0) {
                clear();
                for (Estado estado : filterList) {
                    add(estado);
                    notifyDataSetChanged();
                }
            }
        }
    };
}
