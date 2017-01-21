taskPlanner
===========

podstawowe założenia projektowe:
- zarządzenie kontami użytkowników poprzez implementację FOSUserBundle
- użytkownicy widzą tylko dane wprowadzone przez siebie
- każdy użytkownik może zdefiniować kategorie tematyczne, a następnie przypisywać do nich zadania.
- użytkownik może edytować swoje zadania (chyba że zostały zakończone), usuwać je
- użytkownik może wyświetlić swoje zadania (zakończone i niezakończone) (w tym liczba komentarzy przy każdym z zadań
                                                                   na liście.)
- użytkownik może dodawać / usuwać / zmieniać komentarze do swoich zadań
- komenda konsolowa wysyłająca użytkownikom powiadomienia o zaległych zadaniach do realizacji (do umieszczenia np w cronie)

można by jeszcze zrobić...:
- testy
- taski powtarzalne tzn. taski które będą się automatycznie odnawiały codziennie lub z zadanym interwałem - ich zakończenie (zatwierdzenie jako zrobione) musi się odbywać inaczej niż zwykłych - temat do rozważenia
