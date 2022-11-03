// KRISTY RATH 
// addvid.js: used to validate the add video form by
// flaggin when inputs are invalid in format, invalid in value or invalid in format.
"user strict";

let error = false;

// ensure all content of the DOM is loaded before using selectors
window.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("addvid-form");
    const autofill_btn = document.getElementById("autofill-btn");

    // GET INPUTS 
    let coverimg = document.getElementById("cover");
    let coverurl = document.getElementById("coverlink");
    let title = document.getElementById("title");

    let year = document.getElementById("year");
    let runTime = document.getElementById("runTime");
    let studio = document.getElementById("studio");
    let theatricalRelease = document.getElementById("theatricalRelease");
    let dvdRelease = document.getElementById("dvd-release");
    let actors = document.getElementById("actors");
    let plotSummary = document.getElementById("plot-summary");

    // GET GENRE INPUT FOR API VALUE INSERTION
    let action = document.getElementById("action");
    let commedy = document.getElementById("commedy");
    let scifi = document.getElementById("scifi");
    let thriller = document.getElementById("thriller");
    let romance = document.getElementById("romance");
    let horror = document.getElementById("horror");

    // AUTOFILL BUTTON EVENT LISTENER
    autofill_btn.addEventListener("click", () => {
        // check for autofill only if user entered a title
        if (title.value) {

            // get data from API
            const xhr = new XMLHttpRequest();
            let str = title.value;
            const search = str.replace(" ", "+");
            const url = `https://api.themoviedb.org/3/search/movie?api_key=81751dcdd6465c5c15f2b84fcd6a52ad&query=${search}`;
            console.log(url);
            xhr.open("GET", url);

            xhr.addEventListener("load", (ev) => {
                // reset previous autofills
                console.log(xhr.status);
                action.checked = null;
                commedy.checked = null;
                scifi.checked = null;
                thriller.checked = null;
                romance.checked = null;
                drama.checked = null;
                other.checked = null;
                dvdRelease.value = null;
                plotSummary.value = null;
                coverurl.value = null;

                // check xhr connection
                if (xhr.status == 200) {
                    // parse the top result
                    let results = JSON.parse(xhr.response);
                    console.log(results);
                    results = results.results[0];      

                    // autofill dvd-realease date, plotsummer, cover value and genre checklist
                    dvdRelease.value = results.release_date;
                    plotSummary.value = results.overview;
                    coverurl.value = `https://image.tmdb.org/t/p/w500/${results.poster_path}`;
                    results.genre_ids.forEach((genreID) => {
                        if (genreID == 28) {
                            action.checked = true;
                        }
                        else if (genreID == 35) {
                            commedy.checked = true;
                        }
                        else if (genreID == 878) {
                            scifi.checked = true;
                        }
                        else if (genreID == 53) {
                            thriller.checked = true;
                        }
                        else if (genreID == 10749) {
                            romance.checked = true;
                        }
                        else if (genreID == 18) {
                            drama.checked = true;
                        }
                        else if (genreID == 27) {
                            horror.checked = true;
                        }
                        
                        else {
                            other.checked = true;
                        }
                    });

                }
                else { 
                    console.log("AUTOFILL FAILED");
                }

            })
            xhr.send();


        }
    })
    // PLOT SUMMARY CHARACTER COUNTER
    plotSummary.addEventListener("focus", () => {
        
        plotSummary.addEventListener("input", () => {
            // removes previous message if exists
            const charCount = document.getElementById("charCount"); 
            if (charCount) {
                charCount.remove();
            }
            // insert character count at every character input
            plotSummary.insertAdjacentHTML("afterend", `<span class="infoText" id="charCount">Characters: ${plotSummary.value.length} / 2500 </span>`);
        });

    });

    // PLOT SUMMARY INPUT VALIDATION
    plotSummary.addEventListener("blur", () => {
        // check if previous error message exists
        const plotSummaryError = document.getElementById("plotSummaryError");
        if (plotSummaryError) {
            plotSummaryError.remove();
        }
        // validates whether string is filled
        if (!stringInputIsValid(plotSummary.value)) {
            error = true;
            plotSummary.insertAdjacentHTML("afterend", `<span class="error" id="plotSummaryError">Entry is required. </span>`);
        }
    })
    // YEAR INPUT VALIDATION 
    year.addEventListener("blur", () => {
        let today = new Date();

        const yearError = document.getElementById("yearError");
        if (yearError) {
            yearError.remove();
        }
        // inserts error message when value is null
        if (!year.value) {
            error = true;
            year.insertAdjacentHTML("afterend", `<span class="error" id="yearError">Entry is required. </span>`);
        }
        else { 
            let currentYear = today.getFullYear();
            let validity = false;

            // checkes if current year is not ahead
            (parseInt(year.value) <= currentYear) ? validity = true : validity = false;
            
            // insert error message
            if (!validity) {
                error = true;
                year.insertAdjacentHTML("afterend", `<span class="error" id="yearError">Value cannot be pass the current year. </span>`);
            } 
        }

    });

    // RUNTIME VALIDATION
    runTime.addEventListener("blur", () => {
        const runTimeError = document.getElementById("runTimeError");
        if (runTimeError) {
            runTimeError.remove();
        }

        // checks whether input is entered
        if(!stringInputIsValid(runTime.value)) {
            error = true;
            runTime.insertAdjacentHTML("afterend", `<span class="error" id="runTimeError">Entry is required. </span>`);

        }
    });

    // COVERURL VALIDATION
    coverurl.addEventListener("blur", () => {
        const coverurlError = document.getElementById("coverurlError");
        if (coverurlError) {
            coverurlError.remove();
        }

        // checks whether url includes an img and whether there is either a url or img upload
        if(!imgIsValid(coverurl.value)) {
            error = true;

        }
    });
    // STUDIO VALIDATION 
    studio.addEventListener("blur", () => {
        const studioError = document.getElementById("studioError");
        if (studioError) {
            studioError.remove();
        }

        // checks whether input is filled
        if (!stringInputIsValid(studio.value)) {
            error = true;
            studio.insertAdjacentHTML("afterend", `<span class="error" id="studioError">Entry is required. </span>`);

        }
    })

    // THEATRICAL RELEASE VALIDATION
    theatricalRelease.addEventListener("blur", () => {
        const theatricalDateError = document.getElementById("theatricalDateError");
        if (theatricalDateError) {
            theatricalDateError.remove();
        }
        // checks to see if date is not ahead
        if (!dateIsValid(theatricalRelease.value)) { 
            error = true;
            theatricalRelease.insertAdjacentHTML("afterend", `<span class="error" id="theatricalDateError">Value cannot pass the current date. </span>`);

        }
    });

    // DVD RELEASE VALIDATION
    dvdRelease.addEventListener("blur", () => { 
        const dvdDateError = document.getElementById("dvdDateError");
        if (dvdDateError) {
            dvdDateError.remove();
        }
        // checks to see if date is not ahead

        if (!dateIsValid(dvdRelease.value)) {
            error = true; 
            dvdRelease.insertAdjacentHTML("afterend", `<span class="error" id="dvdDateError">Value cannot pass the current date. </span>`);
        }
    
    });

    // ACTORS VALIDATION 
    actors.addEventListener("blur", () => {
        const actorsError = document.getElementById("actorsError");
        if (actorsError) {
            actorsError.remove();
        }
        if (!stringInputIsValid(actors.value)) {
            error = true;
            actors.insertAdjacentHTML("afterend", `<span class="error" id="actorsError">Entry is required. </span>`);

        }
    })
    // TITLE VALIDATION 
    title.addEventListener("blur", () => {
        const titleError = document.getElementById("titleError");
        if (titleError) {
            titleError.remove();
        }
        if (!stringInputIsValid(title.value)) {
            error = true;
            title.insertAdjacentHTML("afterend", `<span class="error" id="titleError">Entry is required. </span>`);
        }
    })

    // FORM VALIDATION
    form.addEventListener("submit", (ev) => {
        const submitBtn = document.querySelector(".addvid-btn");
        const submitError = document.getElementById("submitError");
        
        // validate check list to see it has at least one selection
        if (!genreIsValid()) {
            error = true;
        }
        // validate check list to see it has at least one selection
        if (!videoTypeIsValid()) {
            error = true;
        }
        if (!imgIsValid()) {
            error = true;
        }
        if (error) {
            if (submitError) {
                submitError.remove();
            }
            submitBtn.insertAdjacentHTML("afterend", `<span class="error" id="submitError">Please fill in all fields with a valid input. </span>`);
            ev.preventDefault();
        }

    });


    // FUNCTIONS 

    // CHECKS FOR URL THAT CONTAINS IMG
    function isValidURL(url) {
        // checks whether url string contains image extenstion
        let urlRegex = "/^https?:\/\/.+\.(jpg|jpeg|png|webp|avif|gif|svg)$/";
        if (url.test(urlRegex)) {
            return true;
        }
        return false;
    }

    // CHECKS FOR AN INPUT FOR IMAGE (EITHER URL OR IMG UPLOAD)
    function imgIsValid() {
        const imgError = document.getElementById("imgError");
        if (imgError) {
            imgError.remove();
        }
        // inserts the appropriate error message
        if (!coverurl.value && !coverimg.checked) {
            coverurl.insertAdjacentHTML("afterend", `<span class="error" id="imgError">Image upload is required. </span>`);
            return false;
        }
        else if (coverurl.value) {
            if (!isValidURL(coverurl.value)) {
                coverurl.insertAdjacentHTML("afterend", `<span class="error" id="imgError">Invalid url. </span>`);
                return false;
            }
        }
        return true;
    }
    // CHECKS FOR INPUT IN GENRE (CALLED ON SUBMISSION)
    function genreIsValid() {
        const genreError = document.getElementById("genreError");
        if (genreError) {
            genreError.remove();
        }
        const drama = document.getElementById("drama");
        const action = document.getElementById("action");
        const horror = document.getElementById("horror");
        const commedy = document.getElementById("commedy");
        const thriller = document.getElementById("thriller");
        const scifi = document.getElementById("scifi");
        const romance = document.getElementById("romance");
    
        if (drama.value=="on" && action.value=="on" && horror.value=="on" && commedy.value=="on" && thriller.value=="on" && scifi.value=="on" && romance.value=="on") {
            const genreSection = document.getElementById("genre-checklist-section");
            genreSection.insertAdjacentHTML("afterend", `<span class="error" id="genreError">Please select at least one genre. </span>`);
            return false;
        }
        return true;
    }
    
    // CHECKS FOR INPUT ON VIDEO TYPE CHECKLIST
    function videoTypeIsValid() {
        const typeError = document.getElementById("typeError");
        if (typeError) {
            typeError.remove();
        }
    
        const dvd = document.getElementById("dvd");
        const bluray = document.getElementById("bluray");
        const fourk = document.getElementById("4k");
        const digital_sd = document.getElementById("digital-sd");
        const digital_hd = document.getElementById("digital-hd");
        const digital_4k = document.getElementById("digital-4k");
    
        if (dvd.value=="on" && bluray.value=="on" && fourk.value=="on" && digital_sd.value=="on" && digital_hd.value=="on" && digital_4k.value=="on") {
            const typeSection = document.getElementById("video-type-checklist-section");
            typeSection.insertAdjacentHTML("afterend", `<span class="error" id="typeError">Please select at least one video type. </span>`);
            return false;
        }
        return true;
    }

    // CHECKS WHETHER THERE IS AN INPUT
    function stringInputIsValid(text) {
        let validity = false;
        (!text.length) ? validity = false : validity = true;
    
        return validity;
    }
    
    // VALIDATE THAT DATE IS NOT AHEAD
    function dateIsValid (dateInput) {
        let validity = false;
        let today = new Date();
        let day = today.getDate();
        let month = today.getMonth() + 1;
        let year = today.getFullYear();
    
        // split string to get day, month, year
        let dateArr = dateInput.split("-");
   
        dateArr.forEach((item) => item = parseInt(item));
        
        // if same day -> valid
        let sameDate = (year == dateArr[0] && month == dateArr[1] && day == dateArr[2]);
        // if same year but previous month -> valid
        let beforeThisMonth = (dateArr[0] == year && dateArr[1] < month);
        // if same year, same month but less days -> valid
        let beforeThisDay = (dateArr[0] == year && dateArr[1] == month && dateArr[2] < day);
    
        // check if at least one scenario applies
        (sameDate || beforeThisMonth || beforeThisDay) ? validity = true : validity == false;

        return validity;
    }

});


