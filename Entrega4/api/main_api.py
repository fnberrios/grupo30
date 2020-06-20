from flask import Flask, render_template, request, abort, json
from pymongo import MongoClient
import pandas as pd
import matplotlib.pyplot as plt
import os
import sys

# Para este ejemplo pediremos la id
# y no la generaremos automáticamente
USER_KEYS = ['uid','name', 'age', 'description']
MSG_KEYS = ['mid', 'message', 'sender','receptant','lat','long','date']

USER = "grupo30"
PASS = "grupo30"
DATABASE = "grupo30"

''' COMO CONECTARSE A NUESTRO SERVIDOR SEGUN AYUDANTÍA
uri = "mongodb://grupoXX:grupoXX@gray.ing.puc.cl/grupoXX"
# La uri 'estándar' es "mongodb://user:password@ip/database"
client = MongoClient(uri)
db = client.get_database()

'''
# El cliente se levanta en la URL de la wiki
# URL = "mongodb://grupoXX:grupoXX@gray.ing.puc.cl/grupoXX"
URL = f"mongodb://{USER}:{PASS}@gray.ing.puc.cl/{DATABASE}"
client = MongoClient(URL)
# Utilizamos la base de datos del grupo
db = client["grupo30"]
# Seleccionamos la collección de usuarios
usuarios = db.users
mensajes = db.messages

'''
Usuarios:
  "uid": <id del usuario>,
  "name": <nombre>,
  "last_name": <apellido>,
  "age": <edad>,
  "occupation": <a qué se dedica>,
  "follows": [<arreglo con una lista de ids de usuarios>]
'''

'''
Mensajes:
  "mid": <id del mensaje>,
  "message": <contenido del mensaje>,
  "sender": <remitente>,
  "receptant": <receptor>,
  "lat": <>,
  "long": <>,
  "date": <fecha>,
'''

# Iniciamos la aplicación de flask
app = Flask(__name__)
app.config['JSONIFY_PRETTYPRINT_REGULAR'] = True


@app.route("/")
def home():
    '''
    Página de inicio
    '''
    return "<h1>¡Hola!</h1>"

# -------------------------- RUTAS TIPO GET --------------------------
# -------------------------- RUTAS BASICAS  --------------------------
@app.route("/messages")
def messages():
    '''
    Obtiene todos los mensajes
    '''
    resultados = list(mensajes.find({},{'_id': 0}))
    return json.jsonify(resultados)

@app.route("/messages/<id>")
def messages_id(id):
    print(f'El id recibido es: {type(id)} {id}')
    resultados = list(mensajes.find({'mid': int(id)}, {'_id': 0}))
    return json.jsonify(resultados)

@app.route("/messages/exchange/<id1>/<id2>")
def messages_intercambiados(id1, id2):
    print(f'id: {id1} y id2: {id2}')
    resultados1 = list(mensajes.find({'sender': int(id1), 'receptant': int(id2)
                                      }, {'_id': 0, 'sender': 1, 'receptant': 1,
                                      'message': 1}))
    resultados2 = list(mensajes.find({'sender': int(id2), 'receptant': int(id1)
                                      }, {'_id': 0, 'sender': 1, 'receptant': 1,
                                          'message': 1}))
    resultados1.append(resultados2[0])

    return json.jsonify(resultados1)


@app.route("/users")
def users():
    '''
    Obtiene todos los usuarios
    '''
    resultados = list(usuarios.find({},{'_id': 0}))
    return json.jsonify(resultados)

@app.route("/users/<id>")
def users_id(id):
    print(f'El id recibido es: {type(id)} {id}')
    user = list(usuarios.find({'uid': int(id)}, {'_id': 0}))
    mensajes_enviados = list(mensajes.find({'sender': int(id)}, {'_id': 0, 'message': 1}))
    todos_los_mensajes = []
    for enviado in mensajes_enviados:
        el_msn_es = enviado['message']
        todos_los_mensajes.append(el_msn_es)
    user[0]['mensajes_enviados'] = todos_los_mensajes

    return json.jsonify(user)


@app.route("/textsearch", methods=['GET'])
def text_search():
    # Se crea el indice invertido
    mensajes.create_index([("message","text")])

    try:
        #Se crea el diccionario que contendra lo entregado por body
        claves = []
        data = {}
        uid = ""

        # Se obtienen los datos entregados por body y se revisa si son entregados efectivamente
        if "desired" in request.json:
            data["desired"] = request.json["desired"]
            claves.append("desired")
        if "required" in request.json:
            data["required"] = request.json["required"]
            claves.append("required")
        if "forbidden" in request.json:
            data["forbidden"] = request.json["forbidden"]
            claves.append("forbidden")
        if "userId" in request.json:
            data["userId"] = request.json["userId"]
            uid = "userId"

        #Se procesa la informacion
        string = ""
        #Caso en el que se entregan dos pares: [desired, required]  [desired, forbidden],  [forbidden, required]
        if (len(claves) > 1):
            if "desired" in claves:
                if len(data["desired"]) != 0:
                    lista1 =[]
                    for i in data["desired"]:
                        lista1.append(f"{i}")
                    string += " ".join(lista1)

            if "required" in claves:
                if len(data["required"]) != 0:
                    lista2 = []
                    for i in data["required"]:
                        lista2.append(f"\"{i}\"")
                    string += " "
                    string += " ".join(lista2)

            if "forbidden" in claves:
                if len(data["forbidden"]) != 0:
                    for i in data["forbidden"]:
                        string += f" -{i}"
            if uid == "":
                resultados = list(mensajes.find({"$text":{"$search": string}}, {"_id": 0, "score": {"$meta": "textScore" }}).sort([("score", {"$meta": "textScore"})]))
                return json.jsonify(resultados)
            if uid == "userId":
                resultados = list(mensajes.find({"$text":{"$search": string}, "sender": int(data["userId"])}, {"_id": 0, "score": {"$meta": "textScore" }}).sort([("score", {"$meta": "textScore"})]))
                return json.jsonify(resultados)

        #Caso en el que se entrega solo forbbiden ------------------------EN PROCESO AUN
        elif ("forbidden" in claves) and (len(claves) == 1):
            resultados = list(mensajes.find({"sender": 43}, {"_id": 0}))
            return json.jsonify(resultados)

        #Caso en el que se entrega el diccionario vacio
        elif (len(claves)==0) and (uid==""):
            resultados = list(mensajes.find({"sender": 43}, {"_id": 0}))
            return json.jsonify(resultados)

    except:
        #Se muestran todos los mensajes en caso de que no se entrega nada
        resultados = list(mensajes.find({}, {"_id": 0}))
        return json.jsonify(resultados)





# -------------------------- RUTAS TIPO POST --------------------------
@app.route("/messages", methods=['POST'])
def post_messages():
    '''
    Crear un mensaje nuevo
    '''
    # En este caso nos entregarán la id del usuario,
    # Y los datos serán ingresados como json
    # Body > raw > JSON en Postman
    try:
        data = {key: request.json[key] for key in MSG_KEYS}
        count = mensajes.count_documents({})
        revisar_ids = list(mensajes.find({}, {'_id': 0, 'mid': 1}))
        todos_ids = []
        for id_ in revisar_ids:
            todos_ids.append(id_['mid'])
        while count in todos_ids:
            count += 1
        data["mid"] = count
        result = mensajes.insert_one(data)
        return json.jsonify({'success': True, 'message': 'Mensaje creado'})

    except:
        print('''EL FORMATO DEL JSON QUE TRATAS DE INGRESASR NO
                    CORRESPONDE CON EL FORMATO DE LA BASE DE DATOS''')



# -------------------------- RUTAS TIPO DELETE --------------------------
@app.route("/messages/delete/<int:mid>", methods=['DELETE'])
def delete_msg(mid):
    '''
    Elimina un mensaje
    '''
    resultado = list(mensajes.find({'mid': int(mid)}, {'_id': 0}))
    if resultado:
        mensajes.delete_one({"mid": mid})
        message = f'mensaje con mid={mid} ha sido eliminado.'
        return json.jsonify({'resulto': 'eliminado', 'message': message})
    else:
        message = f'mid={mid} no existe.'
        return json.jsonify({'resulto': 'no eliminado', 'message': message})


if __name__ == "__main__":
    app.run(debug=True)
    # app.run(debug=True) # Para debuggear!
# ¡Mucho ánimo y éxito! ¡Saludos! :D
