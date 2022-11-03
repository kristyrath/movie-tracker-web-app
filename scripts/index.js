// GENERATES DELETE MODAL


const popup = document.querySelector(".delete-movie-section");
const deleteForm = document.querySelector("#delete-movie-form");

// LISTENER FOR TRASH ICON
document.querySelectorAll(".deleteIcon").forEach(item => {
    item.addEventListener('click', event => {
        // generate pop up


        // get movieid from trashIcon
        let link = item.getAttribute("id");
        let linkArray = link.split("-");
        let movieID = linkArray[linkArray.length - 1];

        // update action in form based on movie ID
        link = "deleteMovie.php?id=" + movieID;
        deleteForm.setAttribute("action", link);
        popup.classList.add("active");
    })
  })


// LISTENER FOR CANCEL BUTTON
const cancelButton = document.querySelector(".cancel-btn");
cancelButton.addEventListener("click", function() {
    popup.classList.remove("active");
});

// LISTENER FOR DELETE BUTTON 
const deleteButton = document.querySelector(".deleteMovie-btn");
deleteButton.addEventListener("click", function() {
    popup.classList.remove("active");

})
