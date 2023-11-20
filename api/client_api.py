import requests

def send_to_bdd(colonnes, values):
    url = "http://sae51.rt-blagnac.fr/api/api.php"

    # Assurez-vous que les colonnes et les valeurs sont des chaînes séparées par des virgules
    data = {
        "token": "zfLMqQseDneEogUe52avGbBdJ8RATdCi9B4eag8cjPy8Qu82qkdffFYXwCcz3n3kV6K9wAeQY2nA6a6UGK38syHwLLfu632FoJ6X",
        "colonnes": ','.join(colonnes),
        "values": ','.join([str(v) for v in values])
    }

    response = requests.post(url, data=data)

    # Gérer les différents codes de statut HTTP
    if response.status_code == 201:
        print("Opération réussie : ", response.text)
    elif response.status_code == 400:
        print(f"Erreur 400 : Mauvaise requête - {response.text}")
    elif response.status_code == 403:
        print(f"Erreur 403 : Accès interdit - {response.text}")
    elif response.status_code == 500:
        print(f"Erreur 500 : Erreur interne du serveur - {response.text}")
    else:
        print(f"Erreur inconnue (HTTP {response.status_code}) : {response.text}")

colonnes = ["ID_CAPTEUR", "VALEUR", "ACTION", "CRITICITE"]
values = [4, 444, "ALLUMER-LUMIERE", 2]

send_to_bdd(colonnes, values)