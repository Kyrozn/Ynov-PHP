// Get modal and elements
var modal = document.getElementById("myModal");
var btn = document.getElementById("editBtn");
var span = document.getElementsByClassName("close")[0];

// Open the modal when the edit button is clicked
if (btn) {
    btn.onclick = function() {
        modal.style.display = "flex";
    }
}

// Close the modal when the 'x' is clicked
if (span) {
    span.onclick = function() {
        modal.style.display = "none";
    }
}

// Close the modal if the user clicks outside the modal content
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
// Fonction pour ajouter une nouvelle entrée de formation
function addExp() {
    const expDiv = document.createElement('div');
    expDiv.innerHTML = `
        <label for="experience">Résumé de l'expérience</label>
        <input type="text" id="Title" name="experienceTitle[]" placeholder="Title" value="<? echo $exp['Title'] ?? "" ?>">
        <input type="text" id="Description" name="experienceDesc[]" placeholder="Description" value="<? echo $exp['Description'] ?? "" ?>">
        <input type="date" id="Description" name="experienceStart[]" placeholder="DateStart" value="<? echo $exp['Start_Date'] ?? "" ?>">
        <input type="date" id="Description" name="experienceEnd[]" placeholder="DateEnd" value="<? echo $exp['End_Date'] ?? "" ?>">
        <button type="button" onclick="remove(this)"class="AddButton">-</button>
        <br><br>
    `;
    document.getElementById('exp-container').appendChild(expDiv);
}

function addSkill() {
    const skillDiv = document.createElement('div');
    skillDiv.innerHTML = `
        <label for="skills">Liste des compétences</label>
        <input type="text" id="Title" name="skillTitle[]" placeholder="Title" value="<? echo $skill['Title'] ?? "" ?>">
        <input type="text" id="Description" name="skillDesc[]" placeholder="Description" value="<? echo $skill['Description'] ?? "" ?>">
        <input type="number" id="Description" name="skillYear[]" placeholder="YearsXP" value="<? echo $skill['YearsXP'] ?? "" ?>">
        <button type="button" onclick="remove(this)" class="AddButton">-</button>
        <br><br>
    `;
    document.getElementById('skill-container').appendChild(skillDiv);
}

function addEducation() {
    const eduDiv = document.createElement('div');
    eduDiv.innerHTML = `
        <label for="education">Formation académique</label>
        <input type="text" id="Title" name="EducationSchool[]" placeholder="School Name" value="<? echo $edu['School'] ?? "" ?>">
        <input type="date" id="Description" name="EducationStart[]" placeholder="DateStart" value="<? echo $edu['Start_Date'] ?? "" ?>">
        <input type="date" id="Description" name="EducationEnd[]" placeholder="DateEnd" value="<? echo $edu['End_Date'] ?? "" ?>">
        <button type="button" onclick="remove(this)" class="AddButton">-</button>
        <br><br>
    `;
    document.getElementById('edu-container').appendChild(eduDiv);
}

// Fonction pour supprimer une entrée de formation
function remove(button) {
    button.parentElement.remove();
}
function prepareHTMLForPDF() {
    // Transforme les inputs text en éléments <p>
    document.querySelectorAll('input[type="text"]').forEach(input => {
        const text = input.value; // Récupère la valeur de l'input
        const p = document.createElement('p'); // Crée un élément <p>
        p.textContent = text; // Assigne le texte
        input.parentNode.replaceChild(p, input); // Remplace l'input par <p>
    });

    // Transforme les inputs de date en éléments <p>
    document.querySelectorAll('input[type="date"]').forEach(input => {
        const text = input.value; // Récupère la valeur de la date
        const p = document.createElement('p');
        p.textContent = text;
        input.parentNode.replaceChild(p, input);
    });

    // Transforme les inputs number en éléments <p>
    document.querySelectorAll('input[type="number"]').forEach(input => {
        const text = input.value; // Récupère la valeur numérique
        const p = document.createElement('p');
        p.textContent = text;
        input.parentNode.replaceChild(p, input);
    });

    // Supprime les boutons d'ajout (+) et autres boutons inutiles
    document.querySelectorAll('.AddButton, .register').forEach(button => {
        button.remove();
    });
}
document.getElementById('pdfForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire
    prepareHTMLForPDF();
    // Récupère le contenu HTML du textarea
    var content = document.getElementById('formCV').innerHTML;
    content = content.replaceAll("<input>")
    // Envoie le contenu au serveur
    fetch('../src/Func/function.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'html=' + encodeURIComponent(content), 
    })
    .then(response => response.blob()) // Récupère le PDF en tant que blob
    .then(blob => {
        // Crée un lien pour télécharger le PDF
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'test.pdf'; // Nom du fichier
        document.body.appendChild(a);
        a.click(); // Simule un clic pour télécharger le PDF
        window.URL.revokeObjectURL(url); // Libère l'URL
        a.remove(); // Supprime l'élément de la page
    })
    .catch(error => console.error('Erreur:', error));
});