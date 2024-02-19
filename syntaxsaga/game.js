const wordDisplay = document.querySelector(".word-display");
const guessesText = document.querySelector(".guesses-text b");
const keyboardDiv = document.querySelector(".keyboard");
const hangmanImage = document.querySelector(".hangman-box img");
const gameModal = document.querySelector(".game-modal");
const playAgainBtn = gameModal.querySelector("button");

let correct = 0;
let currentWord, correctLetters, wrongGuessCount, points;
const maxGuesses = 3;
const pointsPerCorrectAnswer = 10;
const hintCost = 25;

points = 0;

const resetGame = () => {
    correctLetters = [];
    wrongGuessCount = 0;
    hangmanImage.src = "images/hangman-0.svg";
    guessesText.innerText = `${wrongGuessCount} / ${maxGuesses}`;
    wordDisplay.innerHTML = currentWord.split("").map(() => `<li class="letter"></li>`).join("");
    keyboardDiv.querySelectorAll("button").forEach(btn => btn.disabled = false);
    gameModal.classList.remove("show");
}

const resetPoints = () => {
    points = 0;
    document.querySelector(".points-box input").value = points;
}

const getRandomWord = () => {
    if (wordList.length === 0) {
        console.error("Word list is empty!");
        return;
    }

    const { word, hint } = wordList[Math.floor(Math.random() * wordList.length)];
    currentWord = word;
    document.querySelector(".hint-text b").innerText = hint;
    correctLetters = [];
    resetGame();
    wordDisplay.innerHTML = currentWord.split("").map(() => `<li class="letter"></li>`).join("");
}

const updatePoints = () => {
    points += pointsPerCorrectAnswer;
    document.querySelector(".points-box input").value = points;
    correct++;
    updateGuessBox();
}

const gameOver = (isVictory) => {
    const modalText = isVictory ? 'You found the word:' : 'The correct word was:';
    gameModal.querySelector("img").src = `images/${isVictory ? 'victory' : 'lost'}.gif`;
    gameModal.querySelector("h4").innerText = isVictory ? 'Congrats!' : 'Game Over!';
    gameModal.querySelector("p").innerHTML = `${modalText} <b>${currentWord}</b>`;
    gameModal.classList.add("show");
}

const openHintModal = () => {
    document.querySelector(".hint-modal").classList.add("show");
}

const closeHintModal = () => {
    document.querySelector(".hint-modal").classList.remove("show");
}

const chooseHint = (type) => {
    const lettersToPress = type === 'consonant' ?
        Array.from(new Set(currentWord.match(/[^aeiou]/gi))) :
        Array.from(new Set(currentWord.match(/[aeiou]/gi)));

    lettersToPress.forEach(letter => {
        const button = document.querySelector(`.keyboard button[data-letter='${letter}']`);
        if (button && button.disabled) {
            button.disabled = false;
        }
        button.click();
    });

    closeHintModal();
}

const getHint = () => {
    if (points >= hintCost) {
        points -= hintCost;
        document.querySelector(".points-box input").value = points;
        const consonants = Array.from(new Set(currentWord.match(/[^aeiou]/gi)));
        const vowels = Array.from(new Set(currentWord.match(/[aeiou]/gi)));
        if (consonants.length === 0 && vowels.length === 0) {
            console.log("No consonants or vowels in the word!");
        } else {
            openHintModal();
        }
    } else {
        showInsufficientPointsModal();
    }
}

const initGame = (button, clickedLetter) => {
    if (currentWord.includes(clickedLetter)) {
        [...currentWord].forEach((letter, index) => {
            if (letter === clickedLetter) {
                correctLetters.push(letter);
                wordDisplay.querySelectorAll("li")[index].innerText = letter;
                wordDisplay.querySelectorAll("li")[index].classList.add("guessed");
            }
        });

        if (correctLetters.length === currentWord.length) {
            gameOver(true);
            updatePoints();
        }
    } else {
        wrongGuessCount++;
        hangmanImage.src = `images/hangman-${wrongGuessCount}.svg`;
    }

    button.disabled = true;
    guessesText.innerText = `${wrongGuessCount} / ${maxGuesses}`;

    if (wrongGuessCount === maxGuesses) return gameOver(false);
}

for (let i = 97; i <= 122; i++) {
    const button = document.createElement("button");
    const letter = String.fromCharCode(i);
    button.innerText = letter;
    button.dataset.letter = letter;
    keyboardDiv.appendChild(button);
    button.addEventListener("click", () => initGame(button, letter));
}
getRandomWord();
playAgainBtn.addEventListener("click", getRandomWord);

const startGame = () => {
    getRandomWord();
    resetGame();
    resetPoints();
};

const resetGuessCount = () => {
    correct = 0;
    updateGuessBox();
}

document.getElementById("resetButton").addEventListener("click", function () {
    resetGame();
    resetPoints();
    resetGuessCount();
});

document.addEventListener("DOMContentLoaded", function () {
    var hangmanBox = document.querySelector('.hangman-box');
    hangmanBox.style.display = 'none';
});

function redirectToHome() {
    window.location.href = 'index.php';
}

const showInsufficientPointsModal = () => {
    const modal = document.querySelector('.insufficient-points-modal');
    modal.classList.add('show');
    setTimeout(() => {
        modal.classList.remove('show');
    }, 2000);
}
