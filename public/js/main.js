var notification = new Vue({
	el: "#notification",
	delimiters: ['${', '}'],
	data() {
		return {
			showGuests: true,
			showBrowsing: false,
			showLikes: false,
			showLiked: false,
			wached: '',
			browsing: '',
			liked: '',
			likes: '',
			start: 0,
			limit: 15,
			reachedMax: false,
			type: 'guests',
		}
	},
	mounted: function(){
    	this.wached = 'nav-link active',
		this.browsing = 'nav-link',
		this.liked = 'nav-link',
		this.likes = 'nav-link',
		this.getData()
  	},
  	created () {
  		window.addEventListener('scroll', this.handleScroll);
	},
	destroyed () {
  		window.removeEventListener('scroll', this.handleScroll);
	},
	methods: {
		handleScroll () {
			var windowScroll = $(window).scrollTop();
			var windowHeight = $(window).height();
			var documentHeight = $(document).height();

			if((windowScroll + windowHeight + 1) > documentHeight){
				this.getData();		
			}
  		},
		switchTabs(event) {
			this.wached = 'nav-link',
			this.browsing = 'nav-link',
			this.liked = 'nav-link',
			this.likes = 'nav-link',
			this.showGuests = false,
			this.showBrowsing = false,
			this.showLikes = false,
			this.showLiked = false,
			this.start = 0,
			this.reachedMax = false,
			$("#data").html("")

			switch(event.target.innerHTML) {
				case 'Who wached my profile':
					this.wached = 'nav-link active',
					this.showGuests = true,
					this.type = 'guests'
					break;
				case 'Browsing history':
					this.browsing = 'nav-link active',
					this.showBrowsing = true,
					this.type = 'visits'
					break;
				case 'Who liked me':
					this.liked = 'nav-link active',
					this.showLiked = true,
					this.type = 'liked'
					break;
				case 'My likes':
					this.likes = 'nav-link active',
					this.showLikes = true,
					this.type = 'likes'
					break;
			}
			this.getData()
		},

		getData() {
			if(this.reachedMax)
				return;
			let getUrl = window.location;
			let baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

			if(this.showGuests)
				this.type = 'guests';
			else if(this.showBrowsing)
				this.type = 'visits';
			else if(this.showLikes)
				this.type = 'likes';
			else
				this.type = 'liked';

			$.ajax({
				type: "POST",
				url: baseUrl + "/user/notifications",
				data: {
					getData: 1,
					type: this.type,
					start: this.start,
					limit: this.limit
				},
				success: function(response){
					if(response == 'reachedMax')
						notification.reachedMax = true;
					else{
						notification.start += notification.limit;
						//$("#data").html("");
						$("#data").append( response );
					}
				}
			});
		}
	}
});


window.onload = function()
{
	$.ajax({
  url: 'https://tinyfac.es/api/users',
  dataType: 'json',
  success: function(data) {
      console.log(data);
  }
});
}






















