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

document.getElementById('pdfForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire

    // Récupère le contenu HTML du textarea
    var content = document.getElementById('formCV').innerHTML;

    // Envoie le contenu au serveur
    fetch('function.php', {
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
        a.download = 'CV.pdf'; // Nom du fichier
        document.body.appendChild(a);
        a.click(); // Simule un clic pour télécharger le PDF
        window.URL.revokeObjectURL(url); // Libère l'URL
        a.remove(); // Supprime l'élément de la page
    })
    .catch(error => console.error('Erreur:', error));
});