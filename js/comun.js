//funcion de muestreo de mensajes del servidor
function muestraMsg(tipo){
    if (tipo=='inet'){
        msg = "<b>Error:</b> Checa tu conexión a Internet";
        msgIco = 'glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice'
    }else if (tipo=='dbe'){
        msg = "<b>Error:</b> en la base de batos, intenta mas tarde";
        msgIco = 'glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice'
    }else if (tipo=='ok'){
        msg = "Registro Guardado con Exito";
        msgIco = 'fa fa-check alert-icon';
        msgClass_name= 'success-notice';
    }else if (tipo=='delete'){
        msg="Registro Eliminado con Exito";
        msgIco='glyphicon glyphicon-trash';
        msgClass_name= 'success-notice';
    }else if(tipo=='update'){
        msg="Actualización Exitosa";
        msgIco='glyphicon glyphicon-refresh';
        msgClass_name= 'success-notice';
    }else if(tipo=='paramerror'){
        msg="Parametros Incorrectos";
        msgIco='glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice';
    }else if(tipo=='duplicado'){
        msg="<b>Error:</b> No se puede guardar el registro se duplicaría";
        msgIco='glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice';
    }else if(tipo=='duplicado-email'){
        msg="<b>Error:</b> No se puede guardar el registro el correo ya existe";
        msgIco='glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice';
    }else if (tipo=='error-email') {
        msg="<b>Error:</b> al enviar correo, asegurate de introducir correos validos o intentalo mas tarde.";
        msgIco='glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice';
    }else if (tipo=='ok-email') {
        msg = "Correo enviado correctamente.";
        msgIco = 'fa fa-check alert-icon';
        msgClass_name= 'success-notice';
    }else if (tipo=='restablecido') {
        msg = "Tu password a sido restablecido correctamente";
        msgIco = 'fa fa-check alert-icon';
        msgClass_name= 'success-notice';
    }else if (tipo=='bv') {
        msg = "Bienvenido.";
        msgIco = 'fa fa-check alert-icon';
        msgClass_name= 'success-notice';
    }else if(tipo=='error-update'){
        msg="<b>Error:</b> No se pudo actualizar el registro";
        msgIco='glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice';
    }else if(tipo=='error-user'){
        msg="<b>Error:</b> Usuario no encontrado en el sistema";
        msgIco='glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice';
    }else{
        msg="<b>Error:</b> undefined";
        msgIco='glyphicon glyphicon-warning-sign alert-icon';
        msgClass_name= 'error-notice';
    }

    //muestra mensaje
    $.gritter.add({
        title: 'Examen Ingenia',
        text: msg,
        close_icon: 'l-arrows-remove s16',
        icon: msgIco,
        class_name: msgClass_name
    });
}
//funcion de muestreo de mensajes del servidor
function muestraMsg2(msg, msgIco, msgClass_name){
    //muestra mensaje
    $.gritter.add({
        title: 'Examen Ingenia',
        text: msg,
        close_icon: 'l-arrows-remove s16',
        icon: msgIco,
        class_name: msgClass_name
    });
}

