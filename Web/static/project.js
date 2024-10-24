function addCollab() {
  const collabDiv = document.createElement("div");
  collabDiv.innerHTML = `
          <label for="collaborator">Colaborator</label>
                                <input type="text" id="collaborator" name="collaborator[]" placeholder="User" value="<? echo $collab['School'] ?? "" ?>">
        <button type="button" onclick="remove(this)" class="AddButton">-</button>
        <br><br>
    `;
  document.getElementById("Collaborator-container").appendChild(collabDiv);
}

// Fonction pour supprimer une entrée de formation
function remove(button) {
  button.parentElement.remove();
}
