import axios from "axios"

class Like {
    constructor() {
        if(document.querySelector('.like-box')) {
            axios.defaults.headers.common['X-WP-Nonce'] = fuData.nonce
            this.events()
        }
    }

    events() {
        document.querySelector('.like-box').addEventListener('click', e => this.clickDispatcher(e))
    }

    // Class methods
    clickDispatcher(e) {
        let currentLikeBox = e.target
        while(!currentLikeBox.classList.contains('like-box')) {
            currentLikeBox = currentLikeBox.parentElement
        }

        if(currentLikeBox.getAttribute('data-exists') === "yes") {
            this.deleteLike(currentLikeBox)
        } else {
            this.createLike(currentLikeBox)
        }
    }

    async createLike(currentLikeBox) {
        try {
            const response = await axios.post(fuData.root_url + '/wp-json/fu/v1/manageLike', {
                'professorId': currentLikeBox.getAttribute('data-professor')
            })
            if (response.data !== 'Yo dude you need to be logged in to like a professor...') {
                currentLikeBox.setAttribute('data-exists', 'yes')
                let likeCount = parseInt(currentLikeBox.querySelector('.like-count').innerHTML, 10)
                likeCount++
                currentLikeBox.querySelector('.like-count').innerHTML = likeCount
                currentLikeBox.setAttribute('data-like', response.data)
                console.log(response.data)
            } else {
                alert(response.data)
            }
        } catch (e) {
            console.log(e)
        }
    }

    async deleteLike(currentLikeBox) {
        try {
            const response = await axios({
                url: fuData.root_url + '/wp-json/fu/v1/manageLike',
                method: 'delete',
                data: {'like': currentLikeBox.getAttribute('data-like')},
            })
            currentLikeBox.setAttribute('data-exists', 'no')
            let likeCount = parseInt(currentLikeBox.querySelector('.like-count').innerHTML, 10)
            likeCount--
            currentLikeBox.querySelector('.like-count').innerHTML = likeCount
            currentLikeBox.setAttribute('data-like', '')
            console.log(response.data)
        } catch (e) {
            console.log(e)
        }
    }

}

export default Like
