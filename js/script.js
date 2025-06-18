function itsBeenThisLong(dateTime = null, cats) {

    if (!dateTime) {
        console.error('No dateTime provided');
        return;
    }
    // Get current date and time
    let currentDateTime = new Date;
    // 
    //  Convert the provided dateTime string to a Date object
    // Assuming dateTime is in the format 'YYYY-MM-DD HH:MM:SS'
    let fedDateTime = new Date(dateTime); // fedDateTime is in milliseconds since epoch.
    if (isNaN(fedDateTime.getTime())) { // Check if the dateTime is valid by using getTime() which is a method of Date objects that returns the time value in milliseconds since epoch.
        console.error('Invalid dateTime format. Please use "YYYY-MM-DD HH:MM:SS" format.');
        return;
    }
    // Get the time difference in milliseconds
    let timeDifference = currentDateTime - fedDateTime; // subtracting two Date objects gives the difference in milliseconds

    // Here's the bit where you wished you'd paid more attention in maths class.

    // Convert the time difference to seconds, minutes, hours, and days
    // const diffSeconds = Math.floor(timeDifference / 1000); // 1000 milliseconds in a second

    const diffMinutes = Math.floor(timeDifference / (1000 * 60)); // 60 seconds in a minute 

    const diffHours = Math.floor(timeDifference / (1000 * 60 * 60)); // 60 minutes in an hour

    const diffDays = Math.floor(timeDifference / (1000 * 60 * 60 * 24)); // 24 hours in a day


    console.log(`Difference: ${diffDays} days, ${diffHours % 24} hours, ${diffMinutes % 60} minutes, `);

    // Bundle into array and return it.
    let timeDiffArray = [
        { label: 'Days', value: diffDays },
        { label: 'Hours', value: diffHours % 24 },
        { label: 'Minutes', value: diffMinutes % 60 },
        // { label: 'Seconds', value: diffSeconds % 60 }
    ];
    // return the time difference array
    console.log(timeDiffArray);
    return timeDiffArray;


}

function createATable(targetDivId, dataArray = null) {
    // Get Container Div
    let containerDiv = document.getElementById(targetDivId);
    if (!containerDiv) {
        console.error(`No element found with id "${targetDivId}"`);
        return;
    }
    let theTable = document.createElement('table');
    theTable.id = targetDivId + '-table';
    containerDiv.appendChild(theTable);

    return theTable.id;
}

// Lets scrape some up some cat data from out back
function getCatsFromBackEnd() {
    // lets fetch

    fetch('api/get_cat_stats.php', {
        method: 'GET',
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(data => {
            // do something
            console.log("in then");
            console.log(data);
            loopCats(data);
            itsBeenThisLong('2025-06-11 16:05:49', data);
            // return data;
        })
        .catch(error => {
            console.error('AJAX Error:', error);
        });

}

// loop over cats and call it's been this long

function loopCats(catsArray) {

    catsArray.forEach(currentCat => {

        console.log("looping through cat: " + currentCat);

    });

}

/**  Wrap  JS calls in a DOMContentLoaded event so they run after the page is loaded else you get errors telling you odds and sods don't exist. 
 * It's like asking the painter to wait until the house has been built before he tries to paint the non existent walls. 
 * Yeah, I know you knew that. 
 */

document.addEventListener('DOMContentLoaded', function () {
    getCatsFromBackEnd();

    // itsBeenThisLong('2025-06-11 16:05:49', cats);

});