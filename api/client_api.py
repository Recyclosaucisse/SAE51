import requests

def send_to_bdd(sqli_command):
    url = "http://sae51.rt-blagnac.fr/api/mqtt.php"

    data = {
        "token": "zfLMqQseDneEogUe52avGbBdJ8RATdCi9B4eag8cjPy8Qu82qkdffFYXwCcz3n3kV6K9wAeQY2nA6a6UGK38syHwLLfu632FoJ6X",
        "sqli_command": "INSERT INTO `LOCALISATION`(`ID_PIECE`, `ZONE`) VALUES (3,'WC')"
    }

    response = requests.post(url, data=data)

    if response.status_code != 200:
        print(f"Erreur: Code {response.status_code}\nRaison de l'erreur: {response.text}")
    else:
        print(f"Status: {response.status_code}")
        print(response.text)

sqli_command = "INSERT INTO `LOCALISATION`(`ID_PIECE`, `ZONE`) VALUES (3,'WC')"
send_to_bdd(sqli_command)