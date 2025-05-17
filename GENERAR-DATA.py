from faker import Faker
from datetime import datetime, timedelta
import random
from time import time

faker = Faker('es_CL')

NUM_USUARIOS = 200
NUM_TOPICOS = 15
NUM_MAX_ESPECIALIDADES = 3
NUM_ARTICULOS = 500
ANO_MAS_ANTIGUO = 2025 # XD
NUM_MAX_AUTORES = 4
NUM_MAX_TOPICOS = 5
NUM_MAX_REVISORES = 3 # no cambiar este porque si no da errores xd
MAX_TIEMPO = 3

lista_palabras = [ "software", "gestión", "algoritmo", "datos", "tecnología", "información", "programa", "aplicación", "sistema", "procesamiento", "interfaz", "red", "usuario", "plataforma", "virtual", "automatización", "seguridad", "inteligencia", "artificial", "servidor", "código", "desarrollo", "digital", "web", "computación", "modelo", "infraestructura", "proyecto", "ingeniería", "hardware", "actualización", "conectividad", "nube", "depuración", "sintaxis", "función", "entorno", "módulo", "protocolo", "estructura", "lenguaje", "base", "datos", "sistema", "operativo", "dispositivo", "análisis", "rendimiento", "mejora", "colaboración", "innovación", "gestión", "proceso","operativo","ordenador","computador","oficial"]

def generar_rut():
    dv = random.randint(0, 10)
    return f'{random.randint(8,21)}.{random.randint(100,999)}.{random.randint(100,999)}-{"K" if dv == 10 else dv}'

with open("gescon/bd/6_DATA.sql", "w", encoding="utf-8") as f:
    autores = []
    revisores = []
    topicos = []

    # AUTORES
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

    # TOPICOS
    f.write("\n-- INSERTANDO TOPICOS\n")
    lista_posibles_topicos = list(set(lista_palabras))
    random.shuffle(lista_posibles_topicos)
    for i in range(NUM_TOPICOS):
        nombre = lista_posibles_topicos.pop().capitalize()
        topicos.append((i + 1, nombre))
        f.write(f"INSERT INTO Topicos (id,nombre) VALUES ({i+1}, '{nombre}');\n")

    # USUARIOS_ESPECIALIDADES
    f.write("\n-- ASIGNANDO ESPECIALIDADES\n")
    usuarios_especialidades = []
    for rut,_,_,_ in revisores:
        especialidades = set()
        for _ in range(random.randint(1, NUM_MAX_ESPECIALIDADES)):
            esp = random.choice(topicos)[0]
            if esp not in especialidades:
                especialidades.add(esp)
                f.write(f"INSERT INTO Usuarios_Especialidad (rut_usuario,id_topico) VALUES ('{rut}', {esp});\n")
        usuarios_especialidades.append((rut, especialidades))

    # ARTICULOS
    f.write("\n-- CREANDO ARTICULOS\n")
    for id_articulo in range(1,NUM_ARTICULOS):
        titulo = " ".join(random.choices(lista_palabras, k=6)).capitalize()
        resumen = faker.text(max_nb_chars=150).replace("'", "''")
        autores_articulo = random.sample(autores, random.randint(1, NUM_MAX_AUTORES))
        rut_contacto = random.choice(autores_articulo)[0]
        topicos_articulo = random.sample(topicos, random.randint(1, NUM_MAX_TOPICOS))
        
        fecha_i = datetime(ANO_MAS_ANTIGUO,1,1)
        fecha_f = datetime.now()
        fecha_aleatoria = random.uniform(fecha_i.timestamp(), fecha_f.timestamp())
        fecha_envio = datetime.fromtimestamp(fecha_aleatoria)
        fecha_limite = fecha_envio + timedelta(days=7)
        
        fecha_envio = fecha_envio.strftime('%Y-%m-%d %H:%M:%S')
        fecha_limite = fecha_limite.strftime('%Y-%m-%d %H:%M:%S')
        
        ruts_autores = ",".join(autor[0] for autor in autores_articulo)
        id_topicos_articulo = ",".join(str(topico[0]) for topico in topicos_articulo)

        f.write(f"CALL insertar_articulo('1','{titulo}', '{resumen}', '{rut_contacto}','{ruts_autores}' ,'{id_topicos_articulo}');\n")
        f.write(f"UPDATE Articulos SET fecha_envio = '{fecha_envio}', fecha_limite = '{fecha_limite}' WHERE id = {id_articulo};\n")

print("fin del script")