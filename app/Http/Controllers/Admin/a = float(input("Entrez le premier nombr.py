a = float(input("Entrez le premier nombre : "))
b = float(input("Entrez le deuxième nombre : "))

if b != 0:
    total = (a * b) + (a - b) + (a / b)
    print("Résultat final :", total)
else:
    print("Erreur : division par zéro impossible.")