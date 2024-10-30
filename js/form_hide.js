// document.addEventListener(
//     "DOMContentLoaded",
//     function () {
//         // Récupérer le champ "Titre uniquement" par son nom
//         var onlyTitleSwitch = document.querySelector(
//             'input[name="only_title"]'
//         );
//         // Récupérer les champs optionnels à masquer
//         var fieldsToToggle = document.querySelectorAll(".optional-fields");

//         // Fonction pour basculer l'affichage des champs
//         const toggleFields = () => {
//             fieldsToToggle.forEach((field) => {
//                 // Afficher ou masquer en fonction de l'état de onlyTitleSwitch
//                 field.closest(".form-group").style.display =
//                     onlyTitleSwitch && onlyTitleSwitch.checked
//                         ? "none"
//                         : "block";
//             });
//         };

//         // Vérifier l'existence du champ "Titre uniquement" et attacher un écouteur
//         if (onlyTitleSwitch) {
//             onlyTitleSwitch.addEventListener("change", toggleFields);
//             toggleFields(); // Initialisation au chargement
//         }
//     },
//     false
// );

// pour plutards essayé de gerer l'affichage dynamique des champs optionnels
