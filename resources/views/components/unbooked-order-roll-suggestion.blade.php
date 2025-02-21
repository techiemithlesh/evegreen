<!-- Modal Form -->
 
<div class="modal fade modal-lg" id="rollBookForUnbookedOrderModel" tabindex="-1" aria-labelledby="rollBookForUnbookedOrderModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollBookForUnbookedOrderModelLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- <div id="suggestion" style="display: none;">
                    <div style="text-align: center;" id="suggestion1">
                        <div class="example-box movable" style="background-color: rgb(238, 80, 96); right: 20px; width:450px">
                            <div class="header">
                                <span>Roll Stock</span>
                                <span class="icons">▼</span>
                            </div>
                            <div class="content">
                                <div id="suggestionRoll"> a
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center;" id="suggestion2">
                        <div class="example-box movable" style="background-color: rgb(89, 199, 208); width:400px">
                            <div class="header">
                                <span>Roll Transit</span>
                                <span class="icons">▼</span>
                            </div>
                            <div class="content">
                                <div id="suggestionRollTransit">b
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <form action="" id="myForm" class="row g-3">                
                    @csrf
                    <input type="hidden" name="id" id="id" />
                    <input type="hidden" name="booked" id="booked" />
                    <div class="row">                    
                        <div class="row mt-3">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="clientName">Book For Client</label>
                                    <input name="clientName" id="clientName" class="form-control"  readonly />
                                    
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="orderDate">Order Date</label>
                                    <input type="date" name="orderDate" id="orderDate" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="bookingEstimatedDespatchDate">Dispatch Date</label>
                                    <input type="date" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" readonly /> 
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3"> 
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="bagQuality">Bag Quality </label>
                                    <input name="bagQuality" id="bagQuality" class="form-control" readonly />                                        
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="grade">Roll Grade</label>
                                    <input name="grade" id="grade" class="form-control" readonly />  
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="ratePerUnit">Rate Per Unit</label>
                                    <input type="text" name="ratePerUnit" id="ratePerUnit" class="form-control" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3"> 
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="rateType">Rate Type </label>
                                    <input type="text" name="rateType" id="rateType" class="form-control" readonly />
                                </div>
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="fareType">Fare</label>
                                    <input type="text" name="fareType" id="fareType" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="stereoType">Stereo</label>
                                    <input type="text" name="stereoType" id="stereoType" class="form-control" readonly />                                        
                                </div>
                            </div>       
                        </div>

                        <div class="row mt-3">                            
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="bookingBagTypeId">Bag Type </label>
                                    <input type="text" name="bookingBagTypeId" id="bookingBagTypeId" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="bookingBagColor">Bag Color </label>
                                    <input type="text" name="bookingBagColor" id="bookingBagColor" class="form-control" readonly />                                    
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="bookingBagUnits">Bag Unit</label>
                                    <input name="bookingBagUnits" id="bookingBagUnits" class="form-control" readonly />
                                </div>
                            </div>                        
                        </div>

                        <div class="row mt-3"> 
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="totalUnits">QTY</label>
                                    <input name="totalUnits" id="totalUnits" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id='singleGsm'>
                                    <label class="form-label" for="bagGsm">GSM</label>
                                    <input name="bagGsm" id="bagGsm" class="form-control" readonly /> 
                                </div>
                                <div class="form-group" id='multipleGsm' style="display: none;">
                                    <label class="form-label" for="bagGsmJson">GSM</label>
                                    <input name="bagGsmJson" id="bagGsmJson" class="form-control" readonly /> 
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">    
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="w">W</label>
                                    <input name="w" id="w" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="l">L </label>
                                    <input name="l" id="l" class="form-control" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3" id="loopColorDiv"> 
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="looColor">Loop Color</label>
                                    <input name="looColor" id="looColor" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="g">G </label>
                                    <input name="g" id="g" class="form-control" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">      
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="bookingPrintingColor">Printing Color</label>
                                    <input name="bookingPrintingColor" id="bookingPrintingColor" class="form-control" readonly /> 
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="row mt-3">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="altBagColor">Alternate Bag Color</label>
                                    <input name="altBagColor" id="altBagColor" class="form-control" readonly />                                                                                                                                             
                                    <span class="error-text" id="altBagColor-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="altBagGsm">Alternate Gsm</label>
                                    <input name="altBagGsm" id="altBagGsm" class="form-control" readonly />                                                                                                                                            
                                    <span class="error-text" id="altBagGsm-error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row mt-3"  id="suggestion" style="display:none;">
                            <div class="collapsible"  id="suggestion2"> 
                                <div class="panel-title">Transit</div>                                
                                <button type="button" class="collapsible-btn"><i class="bi bi-eye-fill" class="collapsible-btn"></i></button>                            
                                <div class="collapsible-content content" id="suggestionRollTransit" style="overflow-y: scroll;">
                                </div>
                            </div>
                            <div class="collapsible"  id="suggestion1"> 
                                <div class="panel-title">Stock</div>                                
                                <button type="button" class="collapsible-btn"><i class="bi bi-eye-fill" class="collapsible-btn"></i></button>                            
                                <div class="collapsible-content content" id="suggestionRoll" style="overflow-y: scroll;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row mt-3" style="text-align:right">
                            <div > Balance <span id="balance" style="color: red;"> </span> </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12" style="overflow-x: auto;">
                                <table class="table table-bordered  table-responsive " id="orderRoll">
                                    <thead>
                                        <tr>
                                            <th>Roll No</th>
                                            <th>GSM</th>
                                            <th>Roll Color</th>
                                            <th>Length</th>
                                            <th>Size</th>
                                            <th>Net Weight</th>
                                            <th>roll Type</th>
                                            <th>Hardness</th>
                                            <th>Possible Production</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" onclick="setHintCollapse();">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
