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
public class AdaptadorMunicipio extends ArrayAdapter<Municipio> {
    Context context;
    int resource, textViewResourceId;
    List<Municipio> listaMunicipios, listaTemporal, listaSugerencias;

    public AdaptadorMunicipio(Context context, int resource, int textViewResourceId, List<Municipio> listaMunicipios){
        super(context, resource,textViewResourceId, listaMunicipios);
        this.context = context;
        this.resource = resource;
        this.textViewResourceId = textViewResourceId;
        this.listaMunicipios = listaMunicipios;
        listaTemporal = new ArrayList<Municipio>(listaMunicipios);
        listaSugerencias = new ArrayList<Municipio>();
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent){
        View view = convertView;

        if(convertView == null){
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            view = inflater.inflate(R.layout.fila_resultado_estado, parent, false);
        }

        Municipio municipio = listaMunicipios.get(position);
        if(municipio != null){
            TextView nombre = (TextView) view.findViewById(R.id.nombre_estado);
            if(nombre != null){
                nombre.setText(municipio.getNombre());
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
            String str = ((Municipio) resultValue).getNombre();
            return str;
        }

        @Override
        protected FilterResults performFiltering(CharSequence constraint) {
            if (constraint != null) {
                listaSugerencias.clear();
                for (Municipio municipio : listaTemporal) {
                    if (municipio.getNombre().toLowerCase().contains(constraint.toString().toLowerCase())) {
                        listaSugerencias.add(municipio);
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
            List<Municipio> filterList = (ArrayList<Municipio>) results.values;
            if (results != null && results.count > 0) {
                clear();
                for (Municipio municipio : filterList) {
                    add(municipio);
                    notifyDataSetChanged();
                }
            }
        }
    };
}
