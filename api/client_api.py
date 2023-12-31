import requests
import random

def send_to_bdd(values):
    url = "http://sae51.rt-blagnac.fr/api/api.php"

    data = {
        "token": "zfLMqQseDneEogUe52avGbBdJ8RATdCi9B4eag8cjPy8Qu82qkdffFYXwCcz3n3kV6K9wAeQY2nA6a6UGK38syHwLLfu632FoJ6X",
        "values": ','.join([str(v) for v in values])
    }

    response = requests.post(url, data=data)

    # Gérer les différents codes de statut HTTP
    if response.status_code == 201:
        pass
    else:
        print(f"Erreur {response.status_code}: {response.text}")


def generate_values():
    first_value = random.randint(1, 4)    
    second_value = random.randint(1, 775)

    if second_value < 200:
        third_value = "ALLUMER-LUMIERE"
    elif second_value > 500:
        third_value = "ETEINDRE-LUMIERE"
    else:
        third_value = ""
    
    if third_value:
        fourth_value = random.randint(2, 3)
    else:
        fourth_value = random.randint(1, 3)
    
    values = [first_value, second_value, third_value, fourth_value]
    return values


# colonnes = ["ID_CAPTEUR", "VALEUR", "ACTION", "CRITICITE"]
# values = [4, 250, "", 2]

for i in range(25):
    values = generate_values()
    send_to_bdd(values)