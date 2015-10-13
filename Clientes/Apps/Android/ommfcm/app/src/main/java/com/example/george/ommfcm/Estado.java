package com.example.george.ommfcm;

/**
 * Created by George on 10/6/15.
 */
public class Estado {
    private int id;
    private String nombre;

    public Estado(int id, String nombre){
        this.id = id;
        this.nombre = nombre;
    }

    public int getId(){
        return this.id;
    }

    public void setId(int id){
        this.id = id;
    }

    public String getNombre(){
        return this.nombre;
    }

    public void setNombre(String nombre){
        this.nombre = nombre;
    }
}
