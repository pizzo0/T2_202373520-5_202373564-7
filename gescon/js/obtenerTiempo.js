function obtenerTiempo(fechaEnvio) {
    const fecha = new Date(fechaEnvio);
    const fechaActual = new Date();
    const dif = Math.floor((fechaActual - fecha)/1000);
    
    if (dif < 60) {
        return `${dif}s`;
    } else if (dif < 3600) {
        const m = Math.floor(dif/60);
        return `${m}m`;
    } else if (dif < 86400) {
        const h = Math.floor(dif/3600);
        return `${h}h`;
    } else {
        const dia = String(fecha.getDate()).padStart(2,'0');
        const mes = String(fecha.getMonth() + 1).padStart(2,'0');
        const ano = fecha.getFullYear(); // ano xdd
        const h = String(fecha.getHours()).padStart(2,'0');
        const m = String(fecha.getMinutes()).padStart(2,'0');
        return `${dia}-${mes}-${ano} ${h}:${m}`;
    }
}