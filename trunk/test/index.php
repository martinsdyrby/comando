<html>

    <head>
        <title>PHP Comando test</title>

        <link href='http://fonts.googleapis.com/css?family=Andada' rel='stylesheet' type='text/css' />
        <link href="resources/style.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
	    <script type="text/javascript" src="resources/zumo.js"></script>
	    <script type="text/javascript" src="resources/zumo-jquery.js"></script>
	    <script type="text/javascript" src="resources/comando.js"></script>
    </head>

    <body>


        <div id="site">

            <div id="header">
                <img id="logo" src="resources/molalogo.png" alt="MOLAMIL" />
                <ul id="menu">
                </ul>
            </div>

            <div id="page">

            </div>

            <div id="block">
                <div class="resultblock">
                    <div>
                        <div><h1>Result</h1></div>
                        <div id="result"></div>
                    </div>
                </div>
            </div>

        </div>

        <div id="pages">

            <div class="gettest">
                <div><h1>Get Test</h1></div>

                <div>
                    <form action="comando/" method="GET" id="gettestform">
                        <input type="hidden" name="service" value="gettest" />
                        <div>Foo</div>
                        <input type="text" name="foo" />
                        <br />
                        <input type="submit" />
                    </form>
                </div>

            </div>

            <div class="posttest">
                <div><h1>Post Test</h1></div>

                <div>
                    <form action="comando/" method="POST" id="posttestform">
                        <input type="hidden" name="service" value="posttest" />
                        <div>Foo</div>
                        <input type="text" name="foo" />
                        <br />
                        <input type="submit" />
                    </form>
                </div>
            </div>

            <div class="requesttest">
                <div>
                    <table>
                        <tr>
                            <td>
                                <h1>Request Test (GET)</h1>
                                <form action="comando/" method="GET" id="requestgettestform">
                                    <input type="hidden" name="service" value="requesttest" />
                                    <div>Foo</div>
                                    <input type="text" name="foo" />
                                    <br />
                                    <input type="submit" />
                                </form>
                            </td>
                            <td>
                                <h1>Request Test (POST)</h1>
                                <form action="comando/" method="POST" id="requestposttestform">
                                    <input type="hidden" name="service" value="requesttest" />
                                    <div>Foo</div>
                                    <input type="text" name="foo" />
                                    <br />
                                    <input type="submit" />
                                </form>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>



            <div class="scripttest">
                <div>
                    <table>
                        <tr>
                            <td>
                                <h1>Script Test (GET)</h1>
                                <form action="comando/" method="GET" id="scriptgettestform">
                                    <input type="hidden" name="service" value="scripttest" />
                                    <div>Foo</div>
                                    <input type="text" name="foo" />
                                    <br />
                                    <input type="submit" />
                                </form>
                            </td>
                            <td>
                                <h1>Script Test (POST)</h1>
                                <form action="comando/" method="POST" id="scriptposttestform">
                                    <input type="hidden" name="service" value="scripttest" />
                                    <div>Foo</div>
                                    <input type="text" name="foo" />
                                    <br />
                                    <input type="submit" />
                                </form>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>



        </div>




    </body>
</html>