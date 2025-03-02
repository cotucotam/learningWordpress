import $, { post } from 'jquery'
class Search {
    constructor() {
        this.addSearchHTML();
        this.resultsDiv = $("#search-overlay__results")
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term")
        this.events();
        this.typingTimer;
        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;
        this.previousValue
    }

    events() {
        // Prevent duplicate event bindings
        this.openButton.off("click").on("click", (e) => {
            e.preventDefault(); // Prevent default link behavior
            this.openOverlay();
        });

        this.closeButton.off("click").on("click", () => this.closeOverlay());

        // Close search overlay when pressing ESC
        $(document).off("keydown").on("keydown",this.keyPressDispatcher.bind(this));
        this.searchField.on("keyup",this.typingLogic.bind(this))
    }

    typingLogic(){
        if(this.searchField.val() != this.previousValue){
            clearTimeout(this.typingTimer)
            if(this.searchField.val()){
                if(!this.isSpinnerVisible){
                    this.resultsDiv.html('<div class="spinner-loader"></div>')
                    this.isSpinnerVisible = true
                }
            }else{
                this.resultsDiv.html('')
                this.isSpinnerVisible = false

            }
            this.typingTimer = setTimeout(this.getResults.bind(this),1000)
            
        }
        this.previousValue = this.searchField.val()
    }
    getResults(){
        if(this.isSpinnerVisible){
            $.when(              
                $.getJSON(universityData.root_url+'/wp-json/wp/v2/posts?search='+ this.searchField.val()),
                $.getJSON(universityData.root_url+'/wp-json/wp/v2/pages?search='+ this.searchField.val())

             ).then((posts,pages)=>{
                
                var combinedResults = posts[0].concat(pages[0]);
                this.resultsDiv.html(`
                    <h2 class="search-overlay__section-title">General Information</h2>
                    
                    ${combinedResults.length ?`<ul class="link-list min-list">`: `<p>No general information</p>`}
                        ${combinedResults.map(item=>`<li><a href="${item.link}">${item.title.rendered}</a> ${item.type == 'post' ? `by ${item.authorName}`:''}</li>`)}
                    ${combinedResults.length ? `</ul>`:``}
                    `)
  
            },()=>{
                this.resultsDiv.html(`<p>Unexpected error; please try again.</p>`)
            })
            
            // this.resultsDiv.html("<p>Imagine real search results here...</p>").show();
            this.isSpinnerVisible = false
        }
    }
    keyPressDispatcher(e){
        console.log(e.key)
        if (e.key === "Escape" && this.isOverlayOpen) {
            this.closeOverlay();
        }
        if (e.key == 's' && !this.isOverlayOpen){
            this.openOverlay();
        }
    }
    openOverlay() {
        setTimeout(() => this.searchField.trigger('focus'), 301);
        console.log("Overlay Opened");
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll")
        
        this.isOverlayOpen = true
    }

    closeOverlay() {
        console.log("Overlay Closed");
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll")
        this.isOverlayOpen = false
    }

    addSearchHTML(){
        $("body").append(`
            
        <div class="search-overlay">
          <div class="search-overlay__top">
            <div class="container">
              <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
              <input type="text" class="search-term" placeholder="What are you looking for" id="search-term">
              <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
            </div>
          </div>
          <div class="container">
            <div id="search-overlay__results"></div>
          </div>
        </div>
    `)
    }
}

export default Search;
