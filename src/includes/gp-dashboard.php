
<form method="post" action="" id="form-dashboard">

<div class="wrap" >
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Find a Video in our Catalog</h2>
			<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>

		<div id="t">
		  <div id="b">		    
		    <!--************************************************************-->
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span4 watchlist">
						<div class="tabbable">
							<ul class="nav nav-tabs">
								<li class="active">
									<a href="#watchlist-tab1" data-toggle="tab">Watchlist</a>
								</li>
								<li>
									<a href="#watchlist-tab2" data-toggle="tab">Featured Feed</a>
								</li>
								<li>
									<a href="#watchlist-tab3" data-toggle="tab">Hot Videos</a>
								</li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active" id="watchlist-tab1">
									<div class="accordion" id="accordion2">
										<div class="accordion-group">
											<div class="accordion-heading">
												<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">Collapsible Group Item #1</a>
											</div>
											<div id="collapseOne" class="accordion-body collapse in">
												<div class="accordion-inner">
													Anim pariatur cliche...
												</div>
											</div>
										</div>
										<div class="accordion-group">
											<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">Collapsible Group Item #2</a>
											</div>
											<div id="collapseTwo" class="accordion-body collapse">
													<div class="accordion-inner">
														Anim pariatur cliche...
													</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="watchlist-tab2">
									<p>
											Howdy, I'm in Section 2.
									</p>
								</div>
								<div class="tab-pane" id="watchlist-tab3">
									<p>
											Howdy, I'm in Section 3.
									</p>
								</div>
							</div>
						</div>
					</div>
					<div class="span8">
						<div clas="row-fluid">
							<div class="span3 messages">
								<ul class="nav nav-tabs">
										<li class="active">
											<a href="#messages-tab1" data-toggle="tab">messages</a>
										</li>
										<li>
											<a href="#messages-tab2" data-toggle="tab">read</a>
										</li>
										<li>
											<a href="#messages-tab3" data-toggle="tab">allofthem</a>
										</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="messages-tab1">
										<p>
											I'm in Section 1.
										</p>
									</div>
									<div class="tab-pane" id="messages-tab2">
										<p>
											Howdy, I'm in Section 2.
										</p>
									</div>
									<div class="tab-pane" id="messages-tab3">
										<p>
											Howdy, I'm in Section 3.
										</p>
									</div>
								</div>
							</div>
							<div class="span5 feeds">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>
												Feed Name
											</th>
											<th>
												Providers
											</th>
											<th>
												videos
											</th>
											<th>
												watchlist
											</th>
											<th>
												edit
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												feed name
											</td>
											<td>
												all 
											</td>
											<td>
												25
											</td>
											<td>
												<i class="icon-eye-open"></i>
											</td>
											<td>
												<i class="icon-pencil"></i>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div clas="row-fluid">
							<div class="span8 faq">
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#faq-tab1" data-toggle="tab">Resources</a>
									</li>
									<li>
										<a href="#faq-tab2" data-toggle="tab">Tutorials</a>
									</li>
									<li>
										<a href="#faq-tab3" data-toggle="tab">Feedback</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="faq-tab1">
										<p>
											Resources
										</p>
									</div>
									<div class="tab-pane" id="faq-tab2">
										<p>
											Tutorials
										</p>
									</div>
									<div class="tab-pane" id="faq-tab3">
										<p>
											Feedback
										</p>
									</div>
								</div>
							</div>
						</div><!--Body content-->
					</div>
				</div>
			</div>
		    <!--***********************************************************-->		   
		  </div>
		</div>

		<button type="button" id="btn-danger1" class="btn btn-danger">
   Show All</button>

   <button type="button" id="btn-danger2" class="btn btn-danger">
        hide all</button>

	
</div>
</form>
<script type="text/javascript">
	jQuery(function($){	
		$("#btn-danger1").click(function() {
			alert("Handler for .click() called.");
			$('.collapse').not('.in').collapse('show');
		});

		$("#btn-danger2").click(function() {
			alert("Handler for .click() called.");
			$('.collapse.in').collapse('hide');
		});
		
	});
</script>
