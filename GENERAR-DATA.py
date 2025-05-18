from faker import Faker
from datetime import datetime, timedelta
import random
from time import time

faker = Faker('es_CL')

NUM_USUARIOS = 300
NUM_TOPICOS = 15
NUM_MAX_ESPECIALIDADES = 3
NUM_ARTICULOS = 1000
ANO_MAS_ANTIGUO = 2025 # XD
NUM_MAX_AUTORES = 4
NUM_MAX_TOPICOS = 5

lista_palabras = [
    "software", "gestión", "algoritmo", "datos", "tecnología", "información", "programa", "aplicación", "sistema", "procesamiento",
    "interfaz", "red", "usuario", "plataforma", "virtual", "automatización", "seguridad", "inteligencia", "artificial", "servidor",
    "código", "desarrollo", "digital", "web", "computación", "modelo", "infraestructura", "proyecto", "ingeniería", "hardware",
    "actualización", "conectividad", "nube", "depuración", "sintaxis", "función", "entorno", "módulo", "protocolo", "estructura",
    "lenguaje", "base", "operativo", "dispositivo", "análisis", "rendimiento", "mejora", "colaboración", "innovación", "proceso",
    "ordenador", "computador", "oficial", "compilador", "virtualización", "criptografía", "machine", "learning", "deep", "redes",
    "binario", "concurrencia", "simulación", "backend", "frontend", "interoperabilidad", "criptomoneda", "blockchain", "token",
    "firma", "algorítmica", "optimización", "contenedor", "docker", "kubernetes", "api", "documentación", "microservicio",
    "repositorio", "versión", "ram", "cpu", "firmware", "middleware", "renderizado", "pipeline", "query", "framework",
    "biblioteca", "interacción", "latencia", "sintetizador", "navegador", "consola", "depurador", "cluster", "escalabilidad",
    "ancho", "banda", "tokenización", "login", "endpoint", "heurística", "nodo", "ciclo", "variable", "puntero", "stack", "heap",
    "buffer", "overflow", "mutex", "thread", "hilo", "paralelismo", "asíncrono", "sincronización", "bios", "bit", "byte", "hash",
    "checksum", "criptograma", "instrucción", "compresión", "descompresión", "gzip", "json", "xml", "yaml", "parser",
    "serialización", "deserialización", "emulador", "terminal", "shell", "bash", "script", "sniffer", "firewall", "proxy",
    "dns", "http", "https", "tcp", "ip", "ssl", "tls", "puerto", "router", "switch", "ping", "gateway", "vpn", "host", "cliente",
    "cableado", "inalámbrico", "condicional", "bucle", "iteración", "recursividad", "compilación", "interpretación", "editor",
    "ide", "debug", "release", "deploy", "producción", "testing", "unitario", "integración", "benchmark", "log", "error",
    "warning", "observabilidad", "monitorización", "alerta", "evento", "callback", "hook", "estado", "vista", "controlador",
    "singleton", "herencia", "polimorfismo", "abstracción", "modularidad", "acoplamiento", "cohesión"
]

def generar_rut():
    dv = random.randint(0, 10)
    return f'{random.randint(8,21)}.{random.randint(100,999)}.{random.randint(100,999)}-{"K" if dv == 10 else dv}'

with open("gescon/bd/6_DATA.sql", "w", encoding="utf-8") as f:
    f.write("SET NAMES 'utf8mb4';\n")
    
    autores = [] # [rut,nombre,email,rol]
    revisores = [] # [rut,nombre,email,rol]
    topicos = [] # [id,nombre]
    usuarios_especialidades = [] # [rut,{id_topicos/id_especialidad}]

    print("creando usuarios")
    # USUARIOS
    f.write("\n-- INSERTANDO USUARIOS\n")
    for i in range(NUM_USUARIOS):        
        esRevisor = random.randint(1,100) < 30
        
        rut = generar_rut()
        nombre = faker.name().replace("'", "''")
        email = faker.unique.email()
        id_rol = 2 if esRevisor else 1
        
        if (esRevisor):
            revisores.append((rut,nombre,email,id_rol))
        else :
            autores.append((rut,nombre,email,id_rol))
        
        f.write(f"INSERT INTO Usuarios (rut,nombre,email,password,id_rol) VALUES ('{rut}','{nombre}','{email}','USer00','{id_rol}');\n")

    print("creando topicos")
    # TOPICOS
    f.write("\n-- INSERTANDO TOPICOS\n")
    lista_posibles_topicos = list(set(lista_palabras))
    random.shuffle(lista_posibles_topicos)
    for i in range(NUM_TOPICOS):
        nombre = lista_posibles_topicos.pop().capitalize()
        topicos.append((i + 1, nombre))
        f.write(f"INSERT INTO Topicos (id,nombre) VALUES ({i+1}, '{nombre}');\n")

    print("asignando especialidades")
    # USUARIOS_ESPECIALIDADES
    f.write("\n-- ASIGNANDO ESPECIALIDADES\n")
    for rut,_,_,_ in revisores:
        posibles_especialidades = topicos.copy()
        random.shuffle(posibles_especialidades)
        especialidades = set()
        for _ in range(random.randint(1, NUM_MAX_ESPECIALIDADES)):
            esp = posibles_especialidades.pop()[0]
            if esp not in especialidades:
                especialidades.add(esp)
                f.write(f"INSERT INTO Usuarios_Especialidad (rut_usuario,id_topico) VALUES ('{rut}', {esp});\n")
        usuarios_especialidades.append((rut, especialidades))

    print("creando articulos")
    # ARTICULOS
    f.write("\n-- CREANDO ARTICULOS\n")
    for id_articulo in range(1, NUM_ARTICULOS):
        titulo = " ".join(random.choices(lista_palabras, k=6)).capitalize()
        resumen = faker.text(max_nb_chars=150).replace("'", "''")
        autores_articulo = random.sample(autores, random.randint(1, NUM_MAX_AUTORES))
        rut_contacto = random.choice(autores_articulo)[0]
        topicos_articulo = random.sample(topicos, random.randint(1, NUM_MAX_TOPICOS))
        
        fecha_i = datetime(ANO_MAS_ANTIGUO, 1, 1)
        fecha_f = datetime.now()
        fecha_aleatoria = random.uniform(fecha_i.timestamp(), fecha_f.timestamp())
        fecha_envio = datetime.fromtimestamp(fecha_aleatoria)
        fecha_limite = fecha_envio + timedelta(days=7)

        fecha_envio_str = fecha_envio.strftime('%Y-%m-%d %H:%M:%S')
        fecha_limite_str = fecha_limite.strftime('%Y-%m-%d %H:%M:%S')

        # password_articulo = faker.password(length=10) no lo haremos para que sea mas facil probar la base de datos
        password_articulo = 1

        f.write(f"\n-- ARTICULO [{id_articulo}]\n")
        # insertamos articulo
        f.write(f"INSERT INTO Articulos (id, password, titulo, fecha_envio, fecha_limite, resumen, rut_contacto) VALUES ({id_articulo}, '{password_articulo}', '{titulo}', '{fecha_envio_str}', '{fecha_limite_str}', '{resumen}', '{rut_contacto}');\n")

        f.write(f"-- AUTORES [{id_articulo}]\n")
        # insertamos autores
        for autor in autores_articulo:
            f.write(f"INSERT INTO Articulos_Autores (id_articulo, rut_autor) VALUES ({id_articulo}, '{autor[0]}');\n")
        
        f.write(f"-- TOPICOS [{id_articulo}]\n")
        # insertamos topicos
        for topico in topicos_articulo:
            f.write(f"INSERT INTO Articulos_Topicos (id_articulo, id_topico) VALUES ({id_articulo}, {topico[0]});\n")
        
        topicos_articulo_set = set([topico[0] for topico in topicos_articulo])
        
        posibles_revisores = [rut for rut,especialidades in usuarios_especialidades if topicos_articulo_set & especialidades]
        
        revisores_seleccionados = random.sample(posibles_revisores, min(3, len(posibles_revisores)))
        
        f.write(f"-- REVISORES [{id_articulo}]\n")
        for rut_revisor in revisores_seleccionados:
            f.write(f"INSERT INTO Articulos_Revisores (id_articulo, rut_revisor) VALUES ({id_articulo}, '{rut_revisor}');\n")
        
        if fecha_limite < datetime.now():
            f.write(f"-- FORMULARIOS [{id_articulo}]\n")
            for rut_revisor in revisores_seleccionados:
                if random.randint(1,100) < 80:
                    calidad = random.randint(1,7)
                    originalidad = random.randint(1,7)
                    valoracion = random.randint(1,7)
                    argumentos = faker.text(max_nb_chars=200).replace("'", "''")
                    comentarios = faker.text(max_nb_chars=150).replace("'", "''") if random.randint(0,1) else ''
                    
                    f.write(f"INSERT INTO Formulario (id_articulo,rut_revisor,calidad,originalidad,valoracion,argumentos_valoracion,comentarios) VALUES ({id_articulo},'{rut_revisor}',{calidad},{originalidad},{valoracion},'{argumentos}','{comentarios}');\n")


print("fin del script")