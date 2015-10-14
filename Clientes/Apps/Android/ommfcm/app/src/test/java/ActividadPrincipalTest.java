import com.example.george.ommfcm.*;

import android.app.Activity;
import com.example.george.ommfcm.BuildConfig;

import android.app.AlertDialog;
import android.content.Context;
import android.content.Intent;
import android.location.Location;
import android.location.LocationManager;
import android.os.Build;
import org.junit.Before;
import org.junit.Test;
import org.junit.runner.RunWith;
import org.mockito.Mockito;
import org.mockito.internal.matchers.Null;
import org.robolectric.Robolectric;
import org.robolectric.RobolectricGradleTestRunner;
import org.robolectric.RobolectricTestRunner;
import org.robolectric.annotation.Config;
import org.robolectric.shadows.ShadowActivity;
import org.robolectric.shadows.ShadowIntent;

import android.content.Context;
import android.provider.MediaStore;
import android.widget.Button;

import static junit.framework.Assert.assertEquals;
import static junit.framework.Assert.assertFalse;
import static junit.framework.Assert.assertNotNull;
import static junit.framework.Assert.assertNotSame;
import static junit.framework.Assert.assertTrue;
import static org.junit.Assert.assertThat;
import static org.mockito.Mockito.mock;
import static org.robolectric.Shadows.shadowOf;

/**
 * Created by Miguel on 07/10/2015.
 */
@Config(constants = BuildConfig.class, sdk = 21, manifest = "src/main/AndroidManifest.xml")
@RunWith(RobolectricGradleTestRunner.class)
public class ActividadPrincipalTest {

    @Test
    public void test_onCreate() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);
        assertNotNull(activity);
    }

    @Test
    public void test_gpsEstaActivado_servicioActivo() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);
        String context = Context.LOCATION_SERVICE;
        LocationManager mockLocation = (LocationManager) activity.getSystemService(context);

        assertEquals(true, mockLocation.isProviderEnabled(LocationManager.GPS_PROVIDER));
    }
    @Test
    public void test_crearClienteLocalizacionOk() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);
        assertNotNull("El servicio está activo", activity.mGoogleApiClient);
    }
    @Test
    public void test_crearClienteLocalizacionFragmentoNull() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);
        assertNotNull("El servicio ha sido activado", activity.mGoogleApiClient);
    }
    @Test
    public void test_solicitarActivacionGPS() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);
        assertNotNull(activity.solicitarActivacionGPS());
        assertTrue(activity.solicitarActivacionGPS());
    }
    @Test
    public void test_tomar_foto() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);

        Button tomarFoto = (Button) activity.findViewById(R.id.btn_tomar_photo);
        tomarFoto.performClick();
        ShadowActivity shadowActivity = shadowOf(activity);
        Intent startedIntent = shadowActivity.getNextStartedActivity();
        ShadowIntent shadowIntent = shadowOf(startedIntent);

        assertEquals(MediaStore.ACTION_IMAGE_CAPTURE, shadowIntent
                .getComponent().getClassName());
    }
    @Test
    public void test_escoger_foto_galeria() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);

        Button escogerFoto = (Button) activity.findViewById(R.id.btn_escoger_imagen);
        escogerFoto.performClick();
        ShadowActivity shadowActivity = shadowOf(activity);
        Intent startedIntent = shadowActivity.getNextStartedActivity();
        ShadowIntent shadowIntent = shadowOf(startedIntent);

        assertEquals(android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI, shadowIntent
                .getComponent().getClassName());
    }
    @Test
    public void test_tieneCoordenadasImagen() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);
        assertNotNull(activity.tieneCoordenadasImagen("rutaImagen"));
        assertFalse(activity.tieneCoordenadasImagen("rutaImagen"));
    }
    @Test
    public void test_iniciarVistaPrevia() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);

        activity.iniciarVistaPrevia();

        ShadowActivity shadowActivity = shadowOf(activity);
        Intent startedIntent = shadowActivity.getNextStartedActivity();
        ShadowIntent shadowIntent = shadowOf(startedIntent);

        assertEquals("com.example.george.ommfcm.ActividadVistaPrevia", shadowIntent
                .getComponent().getClassName());
    }
    @Test
    public void test_iniciarFormulario() {
        ActividadPrincipal activity = Robolectric.setupActivity(ActividadPrincipal.class);

        activity.iniciarFormulario();

        ShadowActivity shadowActivity = shadowOf(activity);
        Intent startedIntent = shadowActivity.getNextStartedActivity();
        ShadowIntent shadowIntent = shadowOf(startedIntent);

        assertEquals("com.example.george.ommfcm.ActividadFormulario", shadowIntent
                .getComponent().getClassName());
    }
}
