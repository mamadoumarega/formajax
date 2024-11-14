import './styles/app.css';

const formVideo = document.getElementById('formVideo');

formVideo.addEventListener('submit', function (event)  {
    event.preventDefault();

    fetch(this.action, {
        body: new FormData(event.target),
        method: 'POST',

    }).then(r =>{
        return r.json();
    }).then(json => {
        console.log(json);
    });
});

const handleResponse = (response) => {
    switch(response.code) {
        case 200:
            break;
        case 500:
            break;
    }
};