function toggleFullScreen(){
    if(!document.fullscreenElement){
        document.documentElement.requestFullscreen().catch(err => {
            alert(`Error attempting to enable full-screen mode: ${err.message}`);
        })
    }else{
        document.exitFullscreen();
    }
}
window.toggleFullScreen = toggleFullScreen;