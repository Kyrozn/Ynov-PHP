<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .topnav {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            padding: 10px;
            max-height: 70px;
            position: relative;
        }

        .hamburger {
            padding: 15px;
            cursor: pointer;
            background-color: transparent;
            border: none;
            outline: none;
        }

        nav {
            display: none;
            /* Hide by default */
            position: absolute;
            top: 11.3vh;
            right: 0;
            width: 150px;
            background-color: black;
            transition: max-height 0.5s ease;
        }

        nav.show {
            display: block;
            /* Show when toggled */
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            padding: 10px;
            color: white;
            border-bottom: 1px solid white;
            background-color: #333;
            opacity: 0;
            transform: translateY(-20px);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        nav.show ul li {
            opacity: 1;
            transform: translateY(0);
        }

        nav ul li:hover {
            background-color: green;
        }

        .Search {
            background-color: transparent;
            border: none;
            cursor: pointer;
            outline: none;
            height: auto;
            width: auto;
        }

        .animated-image {
            max-width: 40px;
            height: auto;
            display: block;
        }

        .Search.clicked .animated-image {
            transform: rotate(-90deg);
            transition: transform 0.5s ease, opacity 0.5s ease;
        }

        /* Search bar styling */
        #inputBar {
            width: 0;
            opacity: 0;
            border: none;
            padding: 10px;
            transition: width 0.5s ease, opacity 0.5s ease;
            background-color: white;
            border-radius: 5px;
        }

        /* When search is active */
        #inputBar.reveal {
            width: 200px;
            opacity: 1;
        }
    </style>
</head>

<body>
    <div class="topnav">
        <div style="display: flex; align-items: center;">
            <input id="inputBar" placeholder="Search User, Specific Project if he exists" type="text">
            <button class="Search" aria-label="Search">
                <img src="./ressources/loupe.png" alt="Search icon" class="animated-image">
            </button>
        </div>
        <button class="hamburger" type="button" aria-expanded="false" aria-controls="menu">
            <span class="hamburger-box">
                <img src="./ressources/menu.png" alt="Menu icon" class="animated-image">
            </span>
        </button>

        <!-- Menu déroulant -->
        <nav id="menu">
            <ul>
                <li><a href="login.php" style="text-decoration: none; color: white;">Login</a></li>
                <li>Lien n°2</li>
                <li>Lien n°3</li>
                <li>Lien n°4</li>
                <li>Lien n°5</li>
            </ul>
        </nav>
    </div>

    <div style="display: flex;justify-content: center;flex-direction: column;align-items: center;">
        <h2>Welcome To This WebSite</h2>
        <p>You can found Here The different Project Created by our User, See their CVs, And Contact Them</p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const button = document.querySelector(".hamburger");
            const nav = document.querySelector("nav");
            const inputbar = document.querySelector("#inputBar");
            const search = document.querySelector(".Search");

            button.addEventListener("click", function() {
                nav.classList.toggle("show");
                const isExpanded = nav.classList.contains("show");
                button.setAttribute("aria-expanded", isExpanded);
            });

            search.addEventListener("click", function() {
                search.classList.toggle("clicked");
                inputbar.classList.toggle("reveal");
            });
        });
    </script>
</body>

</html>